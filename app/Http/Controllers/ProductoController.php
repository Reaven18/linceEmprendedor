<?php

namespace App\Http\Controllers;

use App\Models\Producto;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

/**
 * @group Productos
 *
 * Endpoints para administración y consulta de productos.
 */
class ProductoController extends Controller
{
    /**
     * Obtener todos los productos
     */
    public function index()
    {
        $productos = Producto::with([
            'vendedor',
            'categorias',
            'imagenes',
            'reviews'
        ])
            ->orderBy('created_at', 'desc')
            ->get();

        return $this->sendResponse(
            $productos,
            'Productos obtenidos correctamente.'
        );
    }

    /**
     * Obtener productos disponibles
     */
    public function disponibles()
    {
        $productos = Producto::disponible()
            ->with([
                'vendedor',
                'categorias',
                'imagenes'
            ])
            ->orderBy('created_at', 'desc')
            ->get();

        return $this->sendResponse(
            $productos,
            'Productos disponibles obtenidos correctamente.'
        );
    }

    /**
     * Obtener producto por ID
     */
    public function show($id)
    {
        $producto = Producto::with([
            'vendedor',
            'categorias',
            'imagenes',
            'reviews.cliente'
        ])->find($id);

        if (!$producto) {

            return $this->sendError(
                'Producto no encontrado.',
                ['error' => 'No existe un producto con ese ID.'],
                404
            );
        }

        return $this->sendResponse(
            $producto,
            'Producto obtenido correctamente.'
        );
    }

    /**
     * Crear producto
     */
    public function store(Request $request)
    {
        $request->validate([
            'nombre' => 'required|string|max:150',
            'descripcion' => 'nullable|string',
            'precio' => 'required|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'es_perecedero' => 'boolean',
            'status' => 'required|in:disponible,agotado,pausado',
            'categorias' => 'nullable|array',
            'categorias.*' => 'exists:categorias,id',
            'imagenes' => 'nullable|array',
            'imagenes.*' => 'url',
            'fotos' => 'nullable|array|max:3',
            'fotos.*' => 'image|mimes:jpg,jpeg,png|max:2048'
        ]);

        $producto = Producto::create([
            'id_vendedor' => Auth::id(),
            'nombre' => $request->nombre,
            'descripcion' => $request->descripcion,
            'precio' => $request->precio,
            'stock' => $request->stock,
            'es_perecedero' => $request->es_perecedero ?? false,
            'status' => $request->status,
        ]);

        // Asociar categorías
        if ($request->has('categorias')) {
            $producto->categorias()->attach($request->categorias);
        }
        if ($request->has('imagenes')) {
            foreach ($request->imagenes as $index => $url) {
                $producto->imagenes()->create([
                    'id_producto' => $producto->id,
                    'url_imagen' => $url,
                    'orden' => $index + 1
                ]);
            }
        } elseif ($request->has('fotos')) {
            foreach ($request->file('fotos') as $index => $foto) {
                $nombre = uniqid('prod_') . '_' . $index . '.' . $foto->getClientOriginalExtension();
                $path = Storage::disk('productos')->putFileAs('imagenes', $foto, $nombre);

                $url = env('SUPABASE_URL') . '/storage/v1/object/public/productos/' . $path;

                $producto->imagenes()->create([
                    'url_imagen' => $url,
                    'orden' => $index + 1
                ]);
            }
        }


        $producto->load([
            'vendedor',
            'categorias',
            'imagenes'
        ]);

        return $this->sendResponse(
            $producto,
            'Producto creado correctamente.',
            201
        );
    }

    /**
     * Actualizar producto
     */
    public function update(Request $request, $id)
    {
        $producto = Producto::find($id);

        if (!$producto) {

            return $this->sendError(
                'Producto no encontrado.',
                ['error' => 'No existe un producto con ese ID.'],
                404
            );
        }

        // Validar propietario
        if (
            $producto->id_vendedor !== Auth::id()
            && !Auth::user()->roles->contains('nombre', 'admin')
        ) {

            return $this->sendError(
                'Acceso denegado.',
                ['error' => 'No puedes modificar este producto.'],
                403
            );
        }

        $request->validate([
            'nombre' => 'sometimes|string|max:150',
            'descripcion' => 'nullable|string',
            'precio' => 'sometimes|numeric|min:0',
            'stock' => 'sometimes|integer|min:0',
            'es_perecedero' => 'sometimes|boolean',
            'status' => 'sometimes|in:disponible,agotado,pausado',
            'categorias' => 'sometimes|array',
            'categorias.*' => 'exists:categorias,id',
            'imagenes' => 'sometimes|array',
            'imagenes.*' => 'url',
            'fotos' => 'sometimes|array|max:3',
            'fotos.*' => 'image|mimes:jpg,jpeg,png|max:2048'
        ]);

        $producto->update($request->only([
            'nombre',
            'descripcion',
            'precio',
            'stock',
            'es_perecedero',
            'status'
        ]));

        // Actualizar categorías
        if ($request->has('categorias')) {
            $producto->categorias()->sync($request->categorias);
        }

        // Actualizar imágenes
        // --- PROCESAR IMÁGENES (Solo si viene alguna de las dos) ---
        if ($request->has('imagenes') || $request->hasFile('fotos')) {

            // 1. LIMPIEZA: Borrar archivos viejos de Supabase
            foreach ($producto->imagenes as $imgVieja) {
                $parts = explode('/productos/', $imgVieja->url_imagen);
                $path = end($parts);
                Storage::disk('productos')->delete($path);
            }

            // 2. Borrar registros viejos en BD
            $producto->imagenes()->delete();

            // 3. Prioridad: Si hay archivos nuevos (fotos)
            if ($request->hasFile('fotos')) {
                foreach ($request->file('fotos') as $index => $foto) {
                    $nombre = uniqid('prod_') . '_' . $index . '.' . $foto->getClientOriginalExtension();
                    $path = Storage::disk('productos')->putFileAs('imagenes', $foto, $nombre);
                    $url = env('SUPABASE_URL') . '/storage/v1/object/public/productos/' . $path;

                    $producto->imagenes()->create(['url_imagen' => $url, 'orden' => $index + 1]);
                }
            }
            // 4. Si no hay archivos, pero hay URLs
            elseif ($request->has('imagenes')) {
                foreach ($request->imagenes as $index => $url) {
                    $producto->imagenes()->create(['url_imagen' => $url, 'orden' => $index + 1]);
                }
            }
        }

        $producto->load([
            'vendedor',
            'categorias',
            'imagenes'
        ]);

        return $this->sendResponse(
            $producto,
            'Producto actualizado correctamente.'
        );
    }

    /**
     * Eliminar producto
     */
    public function destroy($id)
    {
        $producto = Producto::find($id);

        if (!$producto) {

            return $this->sendError(
                'Producto no encontrado.',
                ['error' => 'No existe un producto con ese ID.'],
                404
            );
        }

        // Validar propietario o admin
        if (
            $producto->id_vendedor !== Auth::id()
            && !Auth::user()->roles->contains('nombre', 'admin')
        ) {

            return $this->sendError(
                'Acceso denegado.',
                ['error' => 'No puedes eliminar este producto.'],
                403
            );
        }
        foreach ($producto->imagenes as $img) {
            $parts = explode('/productos/', $img->url_imagen);
            Storage::disk('productos')->delete(end($parts));
        }

        $producto->delete();

        return $this->sendResponse(
            [],
            'Producto eliminado correctamente.'
        );
    }

    /**
     * Obtener productos del usuario autenticado
     */
    public function misProductos()
    {
        $productos = Producto::with([
            'categorias',
            'imagenes',
            'reviews'
        ])
            ->where('id_vendedor', Auth::id())
            ->orderBy('created_at', 'desc')
            ->get();

        return $this->sendResponse(
            $productos,
            'Productos del usuario obtenidos correctamente.'
        );
    }
}
