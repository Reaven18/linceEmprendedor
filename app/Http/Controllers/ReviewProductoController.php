<?php

namespace App\Http\Controllers;

use App\Models\ReviewProductos;
use App\Models\Producto;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * @group Reviews de Productos
 *
 * Endpoints para reseñas de productos.
 */
class ReviewProductoController extends Controller
{
    /**
     * Obtener reviews de un producto
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

        $reviews = ReviewProductos::with([
            'cliente'
        ])
        ->where('id_producto', $idProducto)
        ->where('estado', 'activa')
        ->orderBy('created_at', 'desc')
        ->get();

        return $this->sendResponse(
            $reviews,
            'Reviews obtenidas correctamente.'
        );
    }

    /**
     * Crear review
     */
    public function store(Request $request)
    {
        $request->validate([
            'id_producto' => 'required|exists:productos,id',
            'calificacion' => 'required|integer|min:1|max:5',
            'comentario' => 'nullable|string|max:2000',
            'anonimo' => 'boolean',
        ]);

        // Validar si ya existe review
        $reviewExistente = ReviewProductos::where(
            'id_cliente',
            Auth::id()
        )
        ->where(
            'id_producto',
            $request->id_producto
        )
        ->exists();

        if ($reviewExistente) {

            return $this->sendError(
                'Review duplicada.',
                ['error' => 'Ya has dejado una review para este producto.'],
                409
            );
        }

        $review = ReviewProductos::create([
            'id_cliente' => Auth::id(),
            'id_producto' => $request->id_producto,
            'calificacion' => $request->calificacion,
            'comentario' => $request->comentario,
            'estado' => 'activa',
            'anonimo' => $request->anonimo ?? false,
        ]);

        $review->load([
            'cliente',
            'producto'
        ]);

        return $this->sendResponse(
            $review,
            'Review creada correctamente.',
            201
        );
    }

    /**
     * Obtener review por ID
     */
    public function show($id)
    {
        $review = ReviewProductos::with([
            'cliente',
            'producto'
        ])->find($id);

        if (!$review) {

            return $this->sendError(
                'Review no encontrada.',
                ['error' => 'No existe una review con ese ID.'],
                404
            );
        }

        return $this->sendResponse(
            $review,
            'Review obtenida correctamente.'
        );
    }

    /**
     * Actualizar review
     */
    public function update(Request $request, $id)
    {
        $review = ReviewProductos::find($id);

        if (!$review) {

            return $this->sendError(
                'Review no encontrada.',
                ['error' => 'No existe una review con ese ID.'],
                404
            );
        }

        // Validar propietario
        if (
            $review->id_cliente !== Auth::id()
            && !Auth::user()->roles->contains('nombre', 'admin')
        ) {

            return $this->sendError(
                'Acceso denegado.',
                ['error' => 'No puedes modificar esta review.'],
                403
            );
        }

        $request->validate([
            'calificacion' => 'sometimes|integer|min:1|max:5',
            'comentario' => 'nullable|string|max:2000',
            'estado' => 'sometimes|in:activa,oculta,eliminada',
            'anonimo' => 'sometimes|boolean',
        ]);

        $review->update($request->only([
            'calificacion',
            'comentario',
            'estado',
            'anonimo'
        ]));

        $review->load([
            'cliente',
            'producto'
        ]);

        return $this->sendResponse(
            $review,
            'Review actualizada correctamente.'
        );
    }

    /**
     * Eliminar review
     */
    public function destroy($id)
    {
        $review = ReviewProductos::find($id);

        if (!$review) {

            return $this->sendError(
                'Review no encontrada.',
                ['error' => 'No existe una review con ese ID.'],
                404
            );
        }

        // Validar propietario o admin
        if (
            $review->id_cliente !== Auth::id()
            && !Auth::user()->roles->contains('nombre', 'admin')
        ) {

            return $this->sendError(
                'Acceso denegado.',
                ['error' => 'No puedes eliminar esta review.'],
                403
            );
        }

        $review->delete();

        return $this->sendResponse(
            [],
            'Review eliminada correctamente.'
        );
    }

    /**
     * Obtener reviews del usuario autenticado
     */
    public function misReviews()
    {
        $reviews = ReviewProductos::with([
            'producto.imagenes'
        ])
        ->where('id_cliente', Auth::id())
        ->orderBy('created_at', 'desc')
        ->get();

        return $this->sendResponse(
            $reviews,
            'Reviews del usuario obtenidas correctamente.'
        );
    }
}