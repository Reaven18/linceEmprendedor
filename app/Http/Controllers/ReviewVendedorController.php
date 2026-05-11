<?php

namespace App\Http\Controllers;

use App\Models\ReviewVendedores;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * @group Reviews de Vendedores
 *
 * Endpoints para reseñas de vendedores.
 */
class ReviewVendedorController extends Controller
{
    /**
     * Obtener reviews de un vendedor
     */
    public function index($idVendedor)
    {
        $vendedor = User::find($idVendedor);

        if (!$vendedor) {

            return $this->sendError(
                'Vendedor no encontrado.',
                ['error' => 'No existe un usuario con ese ID.'],
                404
            );
        }

        $reviews = ReviewVendedores::with([
            'cliente'
        ])
        ->where('id_vendedor', $idVendedor)
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
            'id_vendedor' => 'required|exists:usuarios,id',
            'calificacion' => 'required|integer|min:1|max:5',
            'comentario' => 'nullable|string|max:2000',
            'anonimo' => 'boolean',
        ]);

        // Evitar reseñarse a sí mismo
        if ($request->id_vendedor == Auth::id()) {

            return $this->sendError(
                'Operación inválida.',
                ['error' => 'No puedes dejarte una review a ti mismo.'],
                409
            );
        }

        // Validar review duplicada
        $reviewExistente = ReviewVendedores::where(
            'id_cliente',
            Auth::id()
        )
        ->where(
            'id_vendedor',
            $request->id_vendedor
        )
        ->exists();

        if ($reviewExistente) {

            return $this->sendError(
                'Review duplicada.',
                ['error' => 'Ya has dejado una review para este vendedor.'],
                409
            );
        }

        $review = ReviewVendedores::create([
            'id_cliente' => Auth::id(),
            'id_vendedor' => $request->id_vendedor,
            'calificacion' => $request->calificacion,
            'comentario' => $request->comentario,
            'estado' => 'activa',
            'anonimo' => $request->anonimo ?? false,
        ]);

        $review->load([
            'cliente',
            'vendedor'
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
        $review = ReviewVendedores::with([
            'cliente',
            'vendedor'
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
        $review = ReviewVendedores::find($id);

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
            'vendedor'
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
        $review = ReviewVendedores::find($id);

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
     * Obtener reviews hechas por el usuario autenticado
     */
    public function misReviews()
    {
        $reviews = ReviewVendedores::with([
            'vendedor'
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