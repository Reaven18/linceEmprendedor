<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UsuarioController extends Controller
{
    /*
    Listar usuarios
    */
    public function index()
    {
        $usuarios = User::with('roles')->get();

        return $this->sendResponse(
            $usuarios,
            'Usuarios obtenidos con éxito.'
        );
    }
    /*
    Mostrar detalles de un usuario
    */
    public function show($id)
    {
        $usuario = User::with('roles')->find($id);

        if (!$usuario) {
            return $this->sendError(
                'Usuario no encontrado.',
                ['error' => 'No existe un usuario con ese ID.'],
                404
            );
        }

        return $this->sendResponse(
            $usuario,
            'Usuario obtenido con éxito.'
        );
    }
    /*
    Actualizar información de un usuario
     */
    public function update($id, Request $request)
    {
        $usuario = User::find($id);

        if (!$usuario) {
            return $this->sendError(
                'Usuario no encontrado.',
                ['error' => 'No existe un usuario con ese ID.'],
                404
            );
        }

        $request->validate([
            'nombre' => 'sometimes|required|string|max:255',
            'correo' => 'sometimes|required|email|unique:users,correo,' . $id,
            'password' => 'sometimes|required|string|min:6',
            'telefono' => 'sometimes|nullable|string|max:10',
            'carrera' => 'sometimes|nullable|string|max:255',

        ]);

        if ($request->has('nombre')) {
            $usuario->nombre = $request->nombre;
        }
        if ($request->has('correo')) {
            $usuario->correo = $request->correo;
        }
        if ($request->has('password')) {
            $usuario->password = Hash::make($request->password);
        }
        if ($request->has('telefono')) {
            $usuario->telefono = $request->telefono;
        }
        if ($request->has('carrera')) {
            $usuario->carrera = $request->carrera;
        }

        $usuario->save();

        return $this->sendResponse(
            $usuario,
            'Usuario actualizado con éxito.'
        );
    }

    public function actualizarUbicación(Request $request, $id)
    {
        $usuario = User::find($id);

        if (!$usuario) {
            return $this->sendError(
                'Usuario no encontrado.',
                ['error' => 'No existe un usuario con ese ID.'],
                404
            );
        }

        $request->validate([
            'latitud' => 'required|numeric',
            'longitud' => 'required|numeric',
        ]);

        $usuario->latitud = $request->latitud;
        $usuario->longitud = $request->longitud;
        $usuario->save();

        return $this->sendResponse(
            $usuario,
            'Ubicación actualizada con éxito.'
        );

    }

    public function activarNegocio($id)
    {
        $usuario = User::find($id);

        if (!$usuario) {
            return $this->sendError(
                'Usuario no encontrado.',
                ['error' => 'No existe un usuario con ese ID.'],
                404
            );
        }

        $usuario->negocio_activo = !$usuario->negocio_activo ;
        $usuario->save();

        return $this->sendResponse(
            $usuario,
            'Negocio ' . ($usuario->negocio_activo ? 'desactivado' : 'activado') . ' con éxito.'
        );
    }

    public function banearUsuario($id)
    {
        $usuario = User::find($id);

        if (!$usuario) {
            return $this->sendError(
                'Usuario no encontrado.',
                ['error' => 'No existe un usuario con ese ID.'],
                404
            );
        }

        $usuario->baneado = !$usuario->baneado ;
        $usuario->save();

        return $this->sendResponse(
            $usuario,
            'Usuario ' . ($usuario->baneado ? 'baneado' : 'desbaneado') . ' con éxito.'
        );
    }
    

    public function destroy($id)
    {
        $usuario = User::find($id);

        if (!$usuario) {
            return $this->sendError(
                'Usuario no encontrado.',
                ['error' => 'No existe un usuario con ese ID.'],
                404
            );
        }

        $usuario->delete();

        return $this->sendResponse(
            null,
            'Usuario eliminado con éxito.'
        );
    }

}