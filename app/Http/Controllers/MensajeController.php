<?php

namespace App\Http\Controllers;

use App\Models\Mensaje;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * @group Mensajes
 *
 * Endpoints para mensajería entre usuarios.
 */
class MensajeController extends Controller
{
    /**
     * Obtener conversaciones del usuario autenticado
     */
    public function conversaciones()
    {
        $userId = Auth::id();

        $mensajes = Mensaje::with([
            'emisor',
            'receptor'
        ])
        ->where(function ($query) use ($userId) {

            $query->where('id_emisor', $userId)
                  ->orWhere('id_receptor', $userId);

        })
        ->orderBy('created_at', 'desc')
        ->get();

        // Agrupar conversaciones únicas
        $conversaciones = $mensajes
            ->groupBy(function ($mensaje) use ($userId) {

                return $mensaje->id_emisor == $userId
                    ? $mensaje->id_receptor
                    : $mensaje->id_emisor;
            })
            ->map(function ($grupo) {

                return $grupo->first();

            })
            ->values();

        return $this->sendResponse(
            $conversaciones,
            'Conversaciones obtenidas correctamente.'
        );
    }

    /**
     * Obtener conversación con un usuario
     */
    public function chat($idUsuario)
    {
        $usuario = User::find($idUsuario);

        if (!$usuario) {

            return $this->sendError(
                'Usuario no encontrado.',
                ['error' => 'No existe un usuario con ese ID.'],
                404
            );
        }

        $userId = Auth::id();

        $mensajes = Mensaje::where(function ($query) use ($userId, $idUsuario) {

            $query->where('id_emisor', $userId)
                  ->where('id_receptor', $idUsuario);

        })
        ->orWhere(function ($query) use ($userId, $idUsuario) {

            $query->where('id_emisor', $idUsuario)
                  ->where('id_receptor', $userId);

        })
        ->orderBy('created_at', 'asc')
        ->get();

        // Marcar mensajes recibidos como leídos
        Mensaje::where('id_emisor', $idUsuario)
            ->where('id_receptor', $userId)
            ->where('leido', false)
            ->update([
                'leido' => true
            ]);

        return $this->sendResponse(
            $mensajes,
            'Conversación obtenida correctamente.'
        );
    }

    /**
     * Enviar mensaje
     */
    public function store(Request $request)
    {
        $request->validate([
            'id_receptor' => 'required|exists:usuarios,id',
            'contenido' => 'required|string|max:5000',
        ]);

        // Evitar enviarse mensajes a sí mismo
        if ($request->id_receptor == Auth::id()) {

            return $this->sendError(
                'Operación inválida.',
                ['error' => 'No puedes enviarte mensajes a ti mismo.'],
                409
            );
        }

        $mensaje = Mensaje::create([
            'id_emisor' => Auth::id(),
            'id_receptor' => $request->id_receptor,
            'contenido' => $request->contenido,
            'leido' => false,
        ]);

        $mensaje->load([
            'emisor',
            'receptor'
        ]);

        return $this->sendResponse(
            $mensaje,
            'Mensaje enviado correctamente.',
            201
        );
    }

    /**
     * Marcar mensaje como leído
     */
    public function marcarLeido($id)
    {
        $mensaje = Mensaje::find($id);

        if (!$mensaje) {

            return $this->sendError(
                'Mensaje no encontrado.',
                ['error' => 'No existe un mensaje con ese ID.'],
                404
            );
        }

        // Solo el receptor puede marcarlo como leído
        if ($mensaje->id_receptor !== Auth::id()) {

            return $this->sendError(
                'Acceso denegado.',
                ['error' => 'No puedes modificar este mensaje.'],
                403
            );
        }

        $mensaje->update([
            'leido' => true
        ]);

        return $this->sendResponse(
            $mensaje,
            'Mensaje marcado como leído.'
        );
    }

    /**
     * Obtener mensajes no leídos
     */
    public function noLeidos()
    {
        $mensajes = Mensaje::where('id_receptor', Auth::id())
        ->where('leido', false)
        ->orderBy('created_at', 'desc')
        ->get();

        return $this->sendResponse(
            $mensajes,
            'Mensajes no leídos obtenidos correctamente.'
        );
    }

    /**
     * Eliminar mensaje
     */
    public function destroy($id)
    {
        $mensaje = Mensaje::find($id);

        if (!$mensaje) {

            return $this->sendError(
                'Mensaje no encontrado.',
                ['error' => 'No existe un mensaje con ese ID.'],
                404
            );
        }

        // Solo emisor o receptor
        if (
            $mensaje->id_emisor !== Auth::id()
            && $mensaje->id_receptor !== Auth::id()
        ) {

            return $this->sendError(
                'Acceso denegado.',
                ['error' => 'No puedes eliminar este mensaje.'],
                403
            );
        }

        $mensaje->delete();

        return $this->sendResponse(
            [],
            'Mensaje eliminado correctamente.'
        );
    }
}