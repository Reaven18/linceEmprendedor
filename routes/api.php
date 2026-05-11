<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CategoriaController;
use App\Http\Controllers\ImagenProductoController;
use App\Http\Controllers\MensajeController;
use App\Http\Controllers\MetodoPagoController;
use App\Http\Controllers\ProductoController;
use App\Http\Controllers\ReviewProductoController;
use App\Http\Controllers\ReviewVendedorController;
use App\Http\Controllers\TransaccionController;
use App\Http\Controllers\VentaController;

/*
|--------------------------------------------------------------------------
| RUTAS PÚBLICAS
|--------------------------------------------------------------------------
*/

// AUTH
Route::post('/login', [
    AuthController::class,
    'login'
])->name('login');

Route::post('/register', [
    AuthController::class,
    'register'
]);

/*
|--------------------------------------------------------------------------
| CATEGORÍAS
|--------------------------------------------------------------------------
*/

Route::get('/categorias', [
    CategoriaController::class,
    'index'
]);

Route::get('/categorias/{id}', [
    CategoriaController::class,
    'show'
]);

/*
|--------------------------------------------------------------------------
| PRODUCTOS
|--------------------------------------------------------------------------
*/

Route::get('/productos', [
    ProductoController::class,
    'index'
]);

Route::get('/productos/{id}', [
    ProductoController::class,
    'show'
]);

/*
|--------------------------------------------------------------------------
| REVIEWS
|--------------------------------------------------------------------------
*/

Route::get('/reviews/productos/{idProducto}', [
    ReviewProductoController::class,
    'index'
]);

Route::get('/reviews/vendedores/{idVendedor}', [
    ReviewVendedorController::class,
    'index'
]);

/*
|--------------------------------------------------------------------------
| MÉTODOS DE PAGO
|--------------------------------------------------------------------------
*/

Route::get('/metodos-pago', [
    MetodoPagoController::class,
    'index'
]);

Route::get('/metodos-pago/{id}', [
    MetodoPagoController::class,
    'show'
]);

/*
|--------------------------------------------------------------------------
| RUTAS PROTEGIDAS
|--------------------------------------------------------------------------
*/

Route::middleware('auth:sanctum')->group(function () {

    /*
    |--------------------------------------------------------------------------
    | AUTH
    |--------------------------------------------------------------------------
    */

    Route::get('/me', [
        AuthController::class,
        'me'
    ]);

    Route::post('/logout', [
        AuthController::class,
        'logout'
    ]);

    Route::delete('/me', [
        AuthController::class,
        'destroy'
    ]);

    /*
    |--------------------------------------------------------------------------
    | PRODUCTOS
    |--------------------------------------------------------------------------
    */

    Route::post('/productos', [
        ProductoController::class,
        'store'
    ])->middleware('role:vendedor');

    Route::put('/productos/{id}', [
        ProductoController::class,
        'update'
    ])->middleware('role:vendedor');

    Route::delete('/productos/{id}', [
        ProductoController::class,
        'destroy'
    ])->middleware('role:vendedor');

    Route::get('/mis-productos', [
        ProductoController::class,
        'misProductos'
    ])->middleware('role:vendedor');

    /*
    |--------------------------------------------------------------------------
    | CATEGORÍAS
    |--------------------------------------------------------------------------
    */

    Route::post('/categorias', [
        CategoriaController::class,
        'store'
    ])->middleware('role:admin');

    Route::put('/categorias/{id}', [
        CategoriaController::class,
        'update'
    ])->middleware('role:admin');

    Route::delete('/categorias/{id}', [
        CategoriaController::class,
        'destroy'
    ])->middleware('role:admin');

    /*
    |--------------------------------------------------------------------------
    | IMÁGENES PRODUCTO
    |--------------------------------------------------------------------------
    */

    Route::post('/imagenes-producto', [
        ImagenProductoController::class,
        'store'
    ])->middleware('role:vendedor');

    Route::delete('/imagenes-producto/{id}', [
        ImagenProductoController::class,
        'destroy'
    ])->middleware('role:vendedor');

    /*
    |--------------------------------------------------------------------------
    | VENTAS
    |--------------------------------------------------------------------------
    */

    Route::get('/ventas', [
        VentaController::class,
        'index'
    ])->middleware('role:cliente,vendedor');

    Route::get('/ventas/{id}', [
        VentaController::class,
        'show'
    ])->middleware('role:cliente,vendedor');

    Route::post('/ventas', [
        VentaController::class,
        'store'
    ])->middleware('role:cliente');

    Route::post('/ventas/{id}/pagar', [
        VentaController::class,
        'pagar'
    ])->middleware('role:cliente');

    Route::put('/ventas/{id}/cancelar', [
        VentaController::class,
        'cancelar'
    ])->middleware('role:cliente');

    Route::put('/ventas/{id}/completar', [
        VentaController::class,
        'completar'
    ])->middleware('role:admin');

    /*
    |--------------------------------------------------------------------------
    | TRANSACCIONES
    |--------------------------------------------------------------------------
    */

    Route::get('/transacciones', [
        TransaccionController::class,
        'index'
    ])->middleware('role:admin');

    Route::get('/mis-transacciones', [
        TransaccionController::class,
        'misTransacciones'
    ])->middleware('role:cliente');

    Route::get('/transacciones/venta/{idVenta}', [
        TransaccionController::class,
        'porVenta'
    ])->middleware('role:cliente,admin');

    Route::get('/transacciones/{idVenta}/{consecutivo}', [
        TransaccionController::class,
        'show'
    ])->middleware('role:cliente,admin');

    /*
    |--------------------------------------------------------------------------
    | MENSAJES
    |--------------------------------------------------------------------------
    */

    Route::get('/mensajes/conversaciones', [
        MensajeController::class,
        'conversaciones'
    ])->middleware('role:cliente,vendedor');

    Route::get('/mensajes/chat/{idUsuario}', [
        MensajeController::class,
        'chat'
    ])->middleware('role:cliente,vendedor');

    Route::post('/mensajes', [
        MensajeController::class,
        'store'
    ])->middleware('role:cliente,vendedor');

    Route::put('/mensajes/{id}/leido', [
        MensajeController::class,
        'marcarLeido'
    ])->middleware('role:cliente,vendedor');

    Route::get('/mensajes/no-leidos', [
        MensajeController::class,
        'noLeidos'
    ])->middleware('role:cliente,vendedor');

    Route::delete('/mensajes/{id}', [
        MensajeController::class,
        'destroy'
    ])->middleware('role:cliente,vendedor');

    /*
    |--------------------------------------------------------------------------
    | REVIEWS PRODUCTOS
    |--------------------------------------------------------------------------
    */

    Route::post('/reviews/productos', [
        ReviewProductoController::class,
        'store'
    ])->middleware('role:cliente');

    Route::get('/reviews/productos/show/{id}', [
        ReviewProductoController::class,
        'show'
    ])->middleware('role:cliente,vendedor,admin');

    Route::put('/reviews/productos/{id}', [
        ReviewProductoController::class,
        'update'
    ])->middleware('role:cliente,admin');

    Route::delete('/reviews/productos/{id}', [
        ReviewProductoController::class,
        'destroy'
    ])->middleware('role:cliente,admin');

    Route::get('/mis-reviews/productos', [
        ReviewProductoController::class,
        'misReviews'
    ])->middleware('role:cliente');

    /*
    |--------------------------------------------------------------------------
    | REVIEWS VENDEDORES
    |--------------------------------------------------------------------------
    */

    Route::post('/reviews/vendedores', [
        ReviewVendedorController::class,
        'store'
    ])->middleware('role:cliente');

    Route::get('/reviews/vendedores/show/{id}', [
        ReviewVendedorController::class,
        'show'
    ])->middleware('role:cliente,vendedor,admin');

    Route::put('/reviews/vendedores/{id}', [
        ReviewVendedorController::class,
        'update'
    ])->middleware('role:cliente,admin');

    Route::delete('/reviews/vendedores/{id}', [
        ReviewVendedorController::class,
        'destroy'
    ])->middleware('role:cliente,admin');

    Route::get('/mis-reviews/vendedores', [
        ReviewVendedorController::class,
        'misReviews'
    ])->middleware('role:cliente');

    /*
    |--------------------------------------------------------------------------
    | MÉTODOS DE PAGO
    |--------------------------------------------------------------------------
    */

    Route::post('/metodos-pago', [
        MetodoPagoController::class,
        'store'
    ])->middleware('role:admin');

    Route::put('/metodos-pago/{id}', [
        MetodoPagoController::class,
        'update'
    ])->middleware('role:admin');

    Route::delete('/metodos-pago/{id}', [
        MetodoPagoController::class,
        'destroy'
    ])->middleware('role:admin');
});