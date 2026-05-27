<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;



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

    public function clientes()
    {
        $usuarios = User::with('roles')
            ->whereHas('roles', function ($query) {
                $query->where('id', 3);
            })
            ->get();

        return $this->sendResponse(
            $usuarios,
            'Clientes obtenidos con éxito.'
        );
    }
    public function vendedores()
    {
        $usuarios = User::with('roles')
            ->whereHas('roles', function ($query) {
                $query->where('id', 2);
            })
            ->get();

        return $this->sendResponse(
            $usuarios,
            'Vendedores obtenidos con éxito.'
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
    public function updateMe(Request $request)
    {
        $usuario = User::find(Auth::id());

        if (!$usuario) {
            return $this->sendError(
                'Usuario no encontrado.',
                ['error' => 'No existe un usuario con ese ID.'],
                404
            );
        }

        $request->validate([
            'nombre' => 'sometimes|required|string|max:255',
            'correo' => 'sometimes|required|email|unique:users,correo,' . $usuario->id,
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

    public function update(Request $request, $id)
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
            'telefono' => 'sometimes|nullable|string|max:10',
            'carrera' => 'sometimes|nullable|string|max:255',
            'password' => 'sometimes|required|string|min:6',
        ]);

        if ($request->has('nombre')) {
            $usuario->nombre = $request->nombre;
        }
        if ($request->has('telefono')) {
            $usuario->telefono = $request->telefono;
        }
        if ($request->has('carrera')) {
            $usuario->carrera = $request->carrera;
        }
        if ($request->has('password')) {
            $usuario->password = Hash::make($request->password);
        }

        $usuario->save();

        return $this->sendResponse(
            $usuario,
            'Perfil actualizado con éxito.'
        );
    }

    public function ubicacion()
    {
        $usuario = User::find(Auth::id());

        if (!$usuario) {
            return $this->sendError(
                'Usuario no encontrado.',
                ['error' => 'No existe un usuario con ese ID.'],
                404
            );
        }

        return $this->sendResponse(
            [
                'latitud' => $usuario->latitud,
                'longitud' => $usuario->longitud,
            ],
            'Ubicación obtenida con éxito.'
        );
    }

    public function ubicacionUsuario()
    {
        $usuarios = User::with('roles')
            ->whereHas('roles', function ($query) {
                $query->whereIn('id', [2, 3]);
            })
            ->get(['id', 'nombre', 'latitud', 'longitud']);

        return $this->sendResponse(
            $usuarios,
            'Ubicaciones de usuarios obtenidas con éxito.'
        );
    }

    public function vendedoresUbicacion()
    {
        $vendedores = User::with('roles')
            ->whereHas('roles', function ($query) {
                $query->where('id', 2);
            })
            ->get(['id', 'nombre', 'latitud', 'longitud']);

        return $this->sendResponse(
            $vendedores,
            'Ubicaciones de vendedores obtenidas con éxito.'
        );
    }

    public function updateUbicacion(Request $request, $id)
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

    public function updateImagen(Request $request)
    {
        $usuario = Auth::user();

        if (!$usuario) {
            return $this->sendError(
                'Usuario no encontrado.',
                ['error' => 'No existe un usuario con ese ID.'],
                404
            );
        }

        $request->validate([
            'imagen' => 'required|image|mimes:jpg,jpeg,png,gif|max:2048',
        ]);

        try {

            if (!$request->hasFile('imagen')) {
                return $this->sendError(
                    'Archivo no enviado.',
                    ['error' => 'No se recibió ninguna imagen.'],
                    400
                );
            }

            $archivo = $request->file('imagen');

            if(!$usuario->url)
                {
                    $parts = explode('public/usuarios/', $usuario->url);
                    $relativePath = end($parts);
                    if(Storage::disk('usuarios')->exists($relativePath)) {
                        Storage::disk('usuarios')->delete($relativePath);
                    }
                }

            // nombre único
            $nombre = uniqid('perfil_') . '.' .
                $archivo->getClientOriginalExtension();

            // subir a Supabase Storage (S3)
            $path = Storage::disk('usuarios')->putFileAs(
                'perfil',
                $archivo,
                $nombre
            );

            // construir URL manual (Supabase S3 compatible)
            $url = Storage::disk('usuarios')->url($path);


            $usuario->update(['url' => $url]);

            return $this->sendResponse(
                [
                    'url' => $usuario->url
                ],
                'Imagen actualizada correctamente.'
            );
        } catch (\Exception $e) {

            return $this->sendError(
                'Error al subir imagen.',
                [
                    'error' => $e->getMessage()
                ],
                500
            );
        }
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

        $usuario->negocio_activo = !$usuario->negocio_activo;
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

        $usuario->baneado = !$usuario->baneado;
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

    public function destroyMe()
    {
        $usuario = User::find(Auth::id());

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
            'Cuenta eliminada con éxito.'
        );
    }
}
