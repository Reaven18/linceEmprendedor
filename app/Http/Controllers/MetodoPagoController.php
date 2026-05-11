<?php

namespace App\Http\Controllers;

use App\Models\MetodoPago;
use Illuminate\Http\Request;

/**
 * @group Métodos de Pago
 *
 * Endpoints para administrar métodos de pago.
 */
class MetodoPagoController extends Controller
{
    /**
     * Obtener todos los métodos de pago
     */
    public function index()
    {
        $metodos = MetodoPago::orderBy('metodo', 'asc')->get();

        return $this->sendResponse(
            $metodos,
            'Métodos de pago obtenidos correctamente.'
        );
    }

    /**
     * Crear método de pago
     */
    public function store(Request $request)
    {
        $request->validate([
            'metodo' => 'required|string|max:50|unique:metodo_pago,metodo',
        ]);

        $metodo = MetodoPago::create([
            'metodo' => $request->metodo,
        ]);

        return $this->sendResponse(
            $metodo,
            'Método de pago creado correctamente.',
            201
        );
    }

    /**
     * Obtener método de pago por ID
     */
    public function show($id)
    {
        $metodo = MetodoPago::with('transacciones')->find($id);

        if (!$metodo) {

            return $this->sendError(
                'Método de pago no encontrado.',
                ['error' => 'No existe un método de pago con ese ID.'],
                404
            );
        }

        return $this->sendResponse(
            $metodo,
            'Método de pago obtenido correctamente.'
        );
    }

    /**
     * Actualizar método de pago
     */
    public function update(Request $request, $id)
    {
        $metodo = MetodoPago::find($id);

        if (!$metodo) {

            return $this->sendError(
                'Método de pago no encontrado.',
                ['error' => 'No existe un método de pago con ese ID.'],
                404
            );
        }

        $request->validate([
            'metodo' => 'required|string|max:50|unique:metodo_pago,metodo,' . $id,
        ]);

        $metodo->update([
            'metodo' => $request->metodo,
        ]);

        return $this->sendResponse(
            $metodo,
            'Método de pago actualizado correctamente.'
        );
    }

    /**
     * Eliminar método de pago
     */
    public function destroy($id)
    {
        $metodo = MetodoPago::find($id);

        if (!$metodo) {

            return $this->sendError(
                'Método de pago no encontrado.',
                ['error' => 'No existe un método de pago con ese ID.'],
                404
            );
        }

        // Validar si tiene transacciones asociadas
        if ($metodo->transacciones()->count() > 0) {

            return $this->sendError(
                'No se puede eliminar el método de pago.',
                ['error' => 'Existen transacciones asociadas a este método de pago.'],
                409
            );
        }

        $metodo->delete();

        return $this->sendResponse(
            [],
            'Método de pago eliminado correctamente.'
        );
    }
}
