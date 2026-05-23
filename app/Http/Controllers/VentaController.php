<?php

namespace App\Http\Controllers;

use App\Models\Venta;
use App\Models\VentaDetalle;
use App\Models\Producto;
use App\Models\Transaccion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use MercadoPago\Client\Preference\PreferenceClient;
use MercadoPago\MercadoPagoConfig;

/**
 * @group Ventas
 *
 * Endpoints para administración de ventas.
 */
class VentaController extends Controller
{
    /**
     * Obtener ventas del usuario autenticado
     */
    public function misVentas()
    {
        $ventas = Venta::with([
            'cliente',
            'detalles.producto.imagenes',
            'transaccion.metodoPago'
        ])
            ->whereHas('detalles.producto', function ($query) {
                $query->where('id_vendedor', Auth::id());
            })
            ->orderBy('fecha', 'desc')
            ->get();

        return $this->sendResponse(
            $ventas,
            'Ventas obtenidas correctamente.'
        );
    }
    public function misVentasFiltros($lugar, $status, $fecha)
    {
        $query = Venta::with([
            'cliente',
            'detalles.producto.imagenes',
            'transaccion.metodoPago'
        ]);
        if ($lugar != null) {
            $query->where('lugar', 'like', '%' . $lugar . '%');
        }
        if ($status != null) {
            $query->where('status', $status);
        }
        if ($fecha != null) {
            $query->whereDate('fecha', $fecha);
        }
        return $query->get();
    }

    public function misCompras()
    {
        $ventas = Venta::with([
            'detalles.producto.vendedor',
            'transaccion.metodoPago'
        ])
            ->where('id_cliente', Auth::id())
            ->orderBy('fecha', 'desc')
            ->get();

        return $this->sendResponse(
            $ventas,
            'Ventas obtenidas correctamente'
        );
    }

    public function misComprasFiltros($lugar, $status, $fecha)
    {
        $query = Venta::with([
            'detalles.producto.vendedor',
            'transaccion.metodoPago'
        ]);
        if ($lugar != null) {
            $query->where('lugar', 'like', '%' . $lugar . '%');
        }
        if ($status != null) {
            $query->where('status', $status);
        }
        if ($fecha != null) {
            $query->whereDate('fecha', $fecha);
        }
        return $query->get();
    }

    public function indexAdmin()
    {
        $ventas = Venta::with([
            'cliente',
            'detalles.producto.vendedor',
            'transaccion.metodoPago'
        ])
            ->orderBy('fecha', 'desc')
            ->get();

        return $this->sendResponse(
            $ventas,
            'Ventas obtenidas correctamente.'
        );
    }

    public function indexAdminFiltros(Request $request)
    {
        $lugar = $request->input('lugar');
        $status = $request->input('status');
        $fecha = $request->input('fecha');

        $query = Venta::with([
            'cliente',
            'detalles.producto.vendedor',
            'transaccion.metodoPago'
        ]);
        if ($lugar != null) {
            $query->where('lugar', 'like', '%' . $lugar . '%');
        }
        if ($status != null) {
            $query->where('status', $status);
        }
        if ($fecha != null) {
            $query->whereDate('fecha', $fecha);
        }
        return $query->get();
    }

    /**
     * Obtener venta por ID
     */
    public function show($id)
    {
        $venta = Venta::with([
            'cliente',
            'detalles.producto.vendedor',
            'transaccion.metodoPago'
        ])->find($id);

        if (!$venta) {

            return $this->sendError(
                'Venta no encontrada.',
                ['error' => 'No existe una venta con ese ID.'],
                404
            );
        }

        // Validar acceso
        if (
            $venta->id_cliente !== Auth::id()
            && !Auth::user()->roles->contains('nombre', 'admin')
        ) {

            return $this->sendError(
                'Acceso denegado.',
                ['error' => 'No puedes ver esta venta.'],
                403
            );
        }

        return $this->sendResponse(
            $venta,
            'Venta obtenida correctamente.'
        );
    }
    /**
     * Crear venta
     *
     * Tipos:
     * - reservada
     * - pagada
     */
    public function store(Request $request)
    {
        $request->validate([

            'lugar' => 'nullable|string|max:100',
            'latitud' => 'nullable|numeric',
            'longitud' => 'nullable|numeric',
            'tipo' => 'required|in:confirmada,pendiente,pagada',
            'productos' => 'required|array|min:1',
            'productos.*.id_producto' => 'required|exists:productos,id',
            'productos.*.cantidad' => 'required|integer|min:1',

            // Solo requerido si paga al momento
            'id_metodo_de_pago' => 'required_if:tipo,pagada,confirmada|exists:metodo_pago,id',
        ]);

        DB::beginTransaction();

        try {

            // Crear venta
            $venta = Venta::create([
                'id_cliente' => Auth::id(),
                'lugar' => $request->lugar,
                'latitud' => $request->latitud,
                'longitud' => $request->longitud,

                // Si paga al momento => confirmada
                // Si reserva => pendiente
                'status' => $request->tipo === 'pagada'
                    ? 'confirmada'
                    : 'pendiente',

                'fecha' => now(),
            ]);

            $total = 0;

            foreach ($request->productos as $item) {

                $producto = Producto::find($item['id_producto']);

                if (!$producto) {

                    DB::rollBack();

                    return $this->sendError(
                        'Producto no encontrado.',
                        ['error' => 'Uno de los productos no existe.'],
                        404
                    );
                }

                // Validar disponibilidad
                if ($producto->status !== 'disponible') {

                    DB::rollBack();

                    return $this->sendError(
                        'Producto no disponible.',
                        [
                            'error' => 'El producto "' .
                                $producto->nombre .
                                '" no está disponible.'
                        ],
                        409
                    );
                }

                // Validar stock
                if ($producto->stock < $item['cantidad']) {

                    DB::rollBack();

                    return $this->sendError(
                        'Stock insuficiente.',
                        [
                            'error' => 'El producto "' .
                                $producto->nombre .
                                '" no tiene suficiente stock.'
                        ],
                        409
                    );
                }

                $subtotal = $producto->precio * $item['cantidad'];

                // Crear detalle
                VentaDetalle::create([
                    'cantidad' => $item['cantidad'],
                    'precio_unitario' => $producto->precio,
                    'id_venta' => $venta->id,
                    'id_producto' => $producto->id,
                ]);

                $producto->decrement('stock', $item['cantidad']);
                $total += $subtotal;
            }

            // Si paga al momento, crear transacción
            if ($request->tipo === 'confirmada' || $request->tipo === 'pagada') {

                Transaccion::create([
                    'id_venta' => $venta->id,
                    'consecutivo' => 1,
                    'total' => $total,
                    'fecha' => now(),
                    'id_metodo_de_pago' => $request->id_metodo_de_pago,
                ]);
            }

            DB::commit();

            if ($request->tipo === 'confirmada' && $request->id_metodo_de_pago == 2) {
                $this->crearPreferencia($venta->id);
            }

            $venta->load([
                'cliente',
                'detalles.producto.vendedor',
                'transaccion.metodoPago'
            ]);

            return $this->sendResponse(
                $venta,
                'Venta creada correctamente.',
                201
            );
        } catch (\Exception $e) {

            DB::rollBack();

            return $this->sendError(
                'Error al crear la venta.',
                [
                    'error' => $e->getMessage()
                ],
                500
            );
        }
    }

    /**
     * Pagar una venta reservada
     */
    public function pagar(Request $request, $id)
    {
        $venta = Venta::with('detalles')->find($id);

        if (!$venta) {

            return $this->sendError(
                'Venta no encontrada.',
                ['error' => 'No existe una venta con ese ID.'],
                404
            );
        }

        // Validar propietario
        if ($venta->id_cliente !== Auth::id()) {

            return $this->sendError(
                'Acceso denegado.',
                ['error' => 'No puedes pagar esta venta.'],
                403
            );
        }

        // Validar estado
        if ($venta->status !== 'pendiente') {

            return $this->sendError(
                'Venta inválida.',
                ['error' => 'Solo las ventas pendientes pueden pagarse.'],
                409
            );
        }

        $request->validate([
            'id_metodo_de_pago' => 'required|exists:metodo_pago,id',
        ]);

        DB::beginTransaction();

        try {

            $total = 0;

            foreach ($venta->detalles as $detalle) {

                $total += (
                    $detalle->cantidad *
                    $detalle->precio_unitario
                );
            }

            // Crear transacción
            Transaccion::create([
                'id_venta' => $venta->id,
                'consecutivo' => 1,
                'total' => $total,
                'fecha' => now(),
                'id_metodo_de_pago' => $request->id_metodo_de_pago,
            ]);
            // Actualizar estado
            if ($request->id_metodo_de_pago != 2) {

                $venta->update([
                    'status' => 'confirmada'
                ]);
            }

            if ($request->id_metodo_de_pago == 2) {
                $this->crearPreferencia($venta->id);
            }
            DB::commit();

            $venta->load([
                'cliente',
                'detalles.producto',
                'transaccion.metodoPago'
            ]);

            return $this->sendResponse(
                $venta,
                'Venta pagada correctamente.'
            );
        } catch (\Exception $e) {

            DB::rollBack();

            return $this->sendError(
                'Error al procesar el pago.',
                [
                    'error' => $e->getMessage()
                ],
                500
            );
        }
    }
    public function crearPreferencia($id)
    {
        try {

            MercadoPagoConfig::setAccessToken(
                config('services.mercadopago.token')
            );

            $venta = Venta::with('detalles.producto', 'transaccion')
                ->find($id);

            if (!$venta) {

                return $this->sendError(
                    'Venta no encontrada.',
                    ['error' => 'No existe una venta con ese ID.'],
                    404
                );
            }

            if (!$venta->transaccion) {

                return $this->sendError(
                    'Transacción no encontrada.',
                    ['error' => 'La venta no tiene transacción.'],
                    404
                );
            }

            $transaccion = $venta->transaccion;

            $client = new PreferenceClient();
            $items = [];

            foreach ($venta->detalles as $detalle) {

                $items[] = [

                    "title" =>
                    $detalle->producto->nombre,

                    "quantity" =>
                    (int) $detalle->cantidad,

                    "unit_price" =>
                    (float) $detalle->precio_unitario,
                ];
            }

            $preference = $client->create([
                "items" => $items,

                "external_reference" =>
                (string) $transaccion->id

            ]);

            $transaccion->preference_id =
                $preference->id;

            $transaccion->payment_status =
                'pending';

            $transaccion->fecha_pago = now();

            $transaccion->save();

            return $this->sendResponse([
                "preferenceId" =>
                $preference->id
            ], 'Preferencia creada correctamente.');
        } catch (\Exception $e) {

            return $this->sendError(
                'Error al crear preferencia.',
                [
                    'error' => $e->getMessage()
                ],
                500
            );
        }
    }

    /**
     * Cancelar venta
     */
    public function cancelar($id)
    {
        $venta = Venta::find($id);

        if (!$venta) {

            return $this->sendError(
                'Venta no encontrada.',
                ['error' => 'No existe una venta con ese ID.'],
                404
            );
        }

        // Validar acceso
        if (
            $venta->id_cliente !== Auth::id()
            && !Auth::user()->roles->contains('nombre', 'admin')
        ) {

            return $this->sendError(
                'Acceso denegado.',
                ['error' => 'No puedes cancelar esta venta.'],
                403
            );
        }

        // Validar estado
        if ($venta->status === 'cancelada') {

            return $this->sendError(
                'Venta ya cancelada.',
                ['error' => 'La venta ya fue cancelada.'],
                409
            );
        }

        if ($venta->status === 'completada') {

            return $this->sendError(
                'No se puede cancelar.',
                ['error' => 'La venta ya fue completada.'],
                409
            );
        }

        $venta->update([
            'status' => 'cancelada'
        ]);

        return $this->sendResponse(
            $venta,
            'Venta cancelada correctamente.'
        );
    }

    /**
     * Completar venta
     */
    public function completar($id)
    {
        $venta = Venta::find($id);

        if (!$venta) {

            return $this->sendError(
                'Venta no encontrada.',
                ['error' => 'No existe una venta con ese ID.'],
                404
            );
        }

        // Solo admin
        if (!Auth::user()->roles->contains('nombre', 'admin')) {

            return $this->sendError(
                'Acceso denegado.',
                ['error' => 'No tienes permisos para completar ventas.'],
                403
            );
        }

        // Validar estado
        if ($venta->status !== 'confirmada') {

            return $this->sendError(
                'Estado inválido.',
                ['error' => 'Solo ventas confirmadas pueden completarse.'],
                409
            );
        }

        $venta->update([
            'status' => 'completada'
        ]);

        return $this->sendResponse(
            $venta,
            'Venta completada correctamente.'
        );
    }
}
