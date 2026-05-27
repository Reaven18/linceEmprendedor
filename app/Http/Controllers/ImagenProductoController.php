<?php

namespace App\Http\Controllers;

use App\Models\ImagenProducto;
use App\Models\Producto;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

/**
 * @group Imágenes de Productos
 *
 * Endpoints para administrar imágenes de productos.
 */
class ImagenProductoController extends Controller
{
    /**
     * Obtener imágenes de un producto
     */
    public function index($idProducto)
    {
        $producto = Producto::find($idProducto);

        if (!$producto) {

            return $this->sendError(
                'Producto no encontrado.',
                ['error' => 'No existe un producto con ese ID.'],
                404
            );
        }

        $imagenes = ImagenProducto::where('id_producto', $idProducto)
            ->orderBy('orden', 'asc')
            ->get();

        return $this->sendResponse(
            $imagenes,
            'Imágenes obtenidas correctamente.'
        );
    }

    /**
     * Agregar imagen a producto
     */
    public function store(Request $request)
    {

        $request->validate([
            'id_producto' => 'required|exists:productos,id',
            'url_imagen' => 'sometimes|nullable|string|max:255',
            'orden' => 'required|integer|min:1|max:3',
            'imagen' => 'sometimes|image|mimes:jpg,jpeg,png,gif|max:2048',
        ]);

        // Verificar límite de imágenes
        $cantidadImagenes = ImagenProducto::where(
            'id_producto',
            $request->id_producto
        )->count();

        if ($cantidadImagenes >= 3) {

            return $this->sendError(
                'Límite alcanzado.',
                ['error' => 'El producto ya tiene 3 imágenes.'],
                409
            );
        }
        // Verificar orden duplicado
        $ordenExistente = ImagenProducto::where(
            'id_producto',
            $request->id_producto
        )
            ->where('orden', $request->orden)
            ->exists();

        if ($ordenExistente) {

            return $this->sendError(
                'Orden inválido.',
                ['error' => 'Ya existe una imagen con ese orden.'],
                409
            );
        }
        $url = null;

        if ($request->hasFile('imagen'))
        {
            $archivo = $request->file('imagen');
            $nombre = uniqid('producto_') . '.' . $archivo->getClientOriginalExtension();
            $path = Storage::disk('productos')->putFileAs(
                'imagenes',
                $archivo,
                $nombre
            );
             // construir URL manual (Supabase S3 compatible)
             $url = env('SUPABASE_URL')
            . '/storage/v1/object/public/productos/'
            . $path;
        }
        elseif($request->has('url_imagen'))
        {
            $url = $request->url_imagen;
        }

        if(!$url)
        {
            return $this->sendError('Datos faltantes.', ['error' => 'Se debe asignar al menos una URL o una imagen'], 400);
        }

        $imagen = ImagenProducto::create([
            'id_producto' => $request->id_producto,
            'url_imagen' => $url,
            'orden' => $request->orden,
        ]);

        return $this->sendResponse(
            $imagen,
            'Imagen agregada correctamente.',
            201
        );
    }

    /**
     * Obtener una imagen por ID
     */
    public function show($id)
    {
        $imagen = ImagenProducto::with('producto')->find($id);

        if (!$imagen) {

            return $this->sendError(
                'Imagen no encontrada.',
                ['error' => 'No existe una imagen con ese ID.'],
                404
            );
        }

        return $this->sendResponse(
            $imagen,
            'Imagen obtenida correctamente.'
        );
    }

    /**
     * Actualizar imagen
     */
    public function update(Request $request, $id)
    {
        $imagen = ImagenProducto::find($id);

        if (!$imagen) {

            return $this->sendError(
                'Imagen no encontrada.',
                ['error' => 'No existe una imagen con ese ID.'],
                404
            );
        }

        $request->validate([
            'url_imagen' => 'sometimes|string|max:255',
            'orden' => 'sometimes|integer|min:1|max:3',
            'imagen' => 'sometimes|image|mimes:jpg,jpeg,png,gif|max:2048'
        ]);

        // Verificar orden duplicado
        if ($request->has('orden')) {

            $ordenExistente = ImagenProducto::where(
                'id_producto',
                $imagen->id_producto
            )
                ->where('orden', $request->orden)
                ->where('id', '!=', $id)
                ->exists();

            if ($ordenExistente) {

                return $this->sendError(
                    'Orden inválido.',
                    ['error' => 'Ya existe una imagen con ese orden.'],
                    409
                );
            }
        }

         $url = null;

        if ($request->hasFile('imagen'))
        {
            if($imagen->url_imagen)
                {
                    
                    $parts = explode('productos/', $imagen->url_imagen);
                    $relativePath = end($parts);
                    if(Storage::disk('productos')->exists($relativePath)) {
                        Storage::disk('productos')->delete($relativePath);
                    }
                }
            $archivo = $request->file('imagen');
            $nombre = uniqid('producto_') . '.' . $archivo->getClientOriginalExtension();
            $path = Storage::disk('productos')->putFileAs(
                'imagenes',
                $archivo,
                $nombre
            );
             // construir URL manual (Supabase S3 compatible)
             $url = env('SUPABASE_URL')
            . '/storage/v1/object/public/productos/'
            . $path;
        }
        elseif($request->has('url_imagen'))
        {
            $url = $request->url_imagen;
        }

        if($url)
            {
                $imagen->update(['url_imagen' => $url]);
            }

        $imagen->update($request->only([
            'orden'
        ]));

        return $this->sendResponse(
            $imagen,
            'Imagen actualizada correctamente.'
        );
    }

    /**
     * Eliminar imagen
     */
    public function destroy($id)
    {
        $imagen = ImagenProducto::find($id);

        if (!$imagen) {

            return $this->sendError(
                'Imagen no encontrada.',
                ['error' => 'No existe una imagen con ese ID.'],
                404
            );
        }

        $imagen->delete();

        return $this->sendResponse(
            [],
            'Imagen eliminada correctamente.'
        );
    }
}
