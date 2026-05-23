<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

/**
 * @group Gestión de Autenticación
 *
 * Endpoints para administrar el acceso al sistema.
 */
class AuthController extends Controller
{
    /**
     * Inicio de sesión
     */
    public function login(Request $request)
    {
        $request->validate([
            'correo' => 'required|email',
            'password' => 'required|string',
        ]);

        $user = User::where('correo', $request->correo)
            ->with('roles')
            ->first();

        // Verificar credenciales
        if (!$user || !Hash::check($request->password, $user->password)) {

            return $this->sendError(
                'Credenciales incorrectas.',
                ['error' => 'Correo o contraseña inválidos.'],
                401
            );
        }

        // Verificar si el usuario está baneado
        if ($user->baneado) {

            return $this->sendError(
                'Usuario baneado.',
                ['error' => 'Acceso denegado.'],
                403
            );
        }

        // Crear token
        $token = $user->createToken('API_TOKEN')->plainTextToken;

        $success = [
            'token' => $token,
            'user' => $user,
        ];

        return $this->sendResponse(
            $success,
            'Usuario autenticado con éxito.'
        );
    }

    /**
     * Registro de usuario
     */
    public function register(Request $request)
    {
        $request->validate([
            'nombre' => 'required|string|max:100',
            'correo' => 'required|email|unique:usuarios,correo',
            'password' => 'required|string|min:8|confirmed',
            'telefono' => 'nullable|string|max:20',
            'carrera' => 'nullable|string|max:100',
            'id_rol' => 'required|exists:roles,id',
        ]);

        // Crear usuario
        $user = User::create([
            'nombre' => $request->nombre,
            'correo' => $request->correo,
            'password' => Hash::make($request->password),
            'telefono' => $request->telefono,
            'carrera' => $request->carrera,
            'negocio_activo' => false,
            'baneado' => false,
        ]);

        // Asignar rol
        $user->roles()->attach($request->id_rol);

        // Cargar relaciones
        $user->load('roles');

        return $this->sendResponse(
            $user,
            'Usuario registrado correctamente.',
            201
        );
    }

    /**
     * Obtener usuario autenticado
     */
    public function me(Request $request)
    {
        $user = $request->user()->load('roles');
        
        return $this->sendResponse(
            $user,
            'Datos del usuario autenticado.'
        );
    }

    /**
     * Cerrar sesión actual
     */
    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return $this->sendResponse(
            [],
            'Sesión cerrada correctamente.'
        );
    }


//FALTA RUTA PARA CERRAR TODAS LAS SESIONES DE UN USUARIO (ELIMINAR TODOS LOS TOKENS)
//Además de agregarlo a la documentación de OpenAPI
    /**
     * Cerrar todas las sesiones
     */
    public function logoutAll(Request $request)
    {
        $request->user()->tokens()->delete();

        return $this->sendResponse(
            [],
            'Todas las sesiones fueron cerradas.'
        );
    }


}