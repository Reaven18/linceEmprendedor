<?php

namespace App\Http\Controllers;

use App\Models\Transaccion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * @group Transacciones
 *
 * Endpoints para consulta de transacciones.
 */
class TransaccionController extends Controller
{
    /**
     * Obtener todas las transacciones
     */
    public function index()
    {
        $transacciones = Transaccion::with([
            'venta.cliente',
            'metodoPago'
        ])
        ->orderBy('created_at', 'desc')
        ->get();

        return $this->sendResponse(
            $transacciones,
            'Transacciones obtenidas correctamente.'
        );
    }

    /**
     * Obtener transacción por venta y consecutivo
     */
    public function show($idVenta, $consecutivo)
    {
        $transaccion = Transaccion::with([
            'venta.cliente',
            'venta.detalles.producto',
            'metodoPago'
        ])
        ->where('id_venta', $idVenta)
        ->where('consecutivo', $consecutivo)
        ->first();

        if (!$transaccion) {

            return $this->sendError(
                'Transacción no encontrada.',
                ['error' => 'No existe una transacción con esos datos.'],
                404
            );
        }

        // Validar acceso
        if (
            $transaccion->venta->id_cliente !== Auth::id()
            && !Auth::user()->roles->contains('nombre', 'admin')
        ) {

            return $this->sendError(
                'Acceso denegado.',
                ['error' => 'No puedes ver esta transacción.'],
                403
            );
        }

        return $this->sendResponse(
            $transaccion,
            'Transacción obtenida correctamente.'
        );
    }

    /**
     * Obtener transacciones del usuario autenticado
     */
    public function misTransacciones()
    {
        $transacciones = Transaccion::with([
            'venta',
            'metodoPago'
        ])
        ->whereHas('venta', function ($query) {

            $query->where('id_cliente', Auth::id());

        })
        ->orderBy('created_at', 'desc')
        ->get();

        return $this->sendResponse(
            $transacciones,
            'Transacciones del usuario obtenidas correctamente.'
        );
    }

    /**
     * Obtener transacciones de una venta
     */
    public function porVenta($idVenta)
    {
        $transacciones = Transaccion::with([
            'metodoPago'
        ])
        ->where('id_venta', $idVenta)
        ->orderBy('consecutivo', 'asc')
        ->get();

        if ($transacciones->isEmpty()) {

            return $this->sendError(
                'No se encontraron transacciones.',
                ['error' => 'La venta no tiene transacciones registradas.'],
                404
            );
        }

        // Validar acceso usando la primera transacción
        $venta = $transacciones->first()->venta;

        if (
            $venta->id_cliente !== Auth::id()
            && !Auth::user()->roles->contains('nombre', 'admin')
        ) {

            return $this->sendError(
                'Acceso denegado.',
                ['error' => 'No puedes ver estas transacciones.'],
                403
            );
        }

        return $this->sendResponse(
            $transacciones,
            'Transacciones obtenidas correctamente.'
        );
    }
}
