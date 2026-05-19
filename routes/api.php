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
//Validada

/**
 * Inicio de sesión
 * Parametros de Body:
 * {
 *   "correo" : "",
 *   "password": ""
 *   }

 */
Route::post('/login', [
    AuthController::class,
    'login'
])->name('login');


//Validada
/**
 * Registro de usuario
 * Parametros de Body:
 *{
 *   "nombre" : "",
 *   "correo" : "",
 *   "password": "",
 *   "password_confirmation": "",
 *   "telefono": "",
 *   "carrera": "",
 *   "id_rol":
 * }
 */
Route::post('/register', [
    AuthController::class,
    'register'
]);

/*
|--------------------------------------------------------------------------
| CATEGORÍAS
|--------------------------------------------------------------------------
*/

//Validada
Route::get('/categorias', [
    CategoriaController::class,
    'index'
]);

//Validada
/**
 * Respuesta 200
 * {
 *   "success": true,
 *   "data": {
 *       "id": 2,
 *       "nombre": "Bebidas",
 *       "productos": []
 *   },
 *   "message": "Categoría obtenida correctamente."
 * }
 */
Route::get('/categorias/{id}', [
    CategoriaController::class,
    'show'
]);

/*
|--------------------------------------------------------------------------
| PRODUCTOS
|--------------------------------------------------------------------------
*/

//validada
Route::get('/productos', [
    ProductoController::class,
    'index'
]);

//validada
Route::get('/productos/{id}', [
    ProductoController::class,
    'show'
]);

/*
|--------------------------------------------------------------------------
| REVIEWS
|--------------------------------------------------------------------------
*/
//validada
/*
* Respuesta 200
{
    "success": true,
    "data": [
        {
            "id": 1,
            "id_cliente": 2,
            "id_producto": 3,
            "calificacion": 4,
            "comentario": "Esta en buen estado, solo que tardó más de lo esperado",
            "estado": "activa",
            "anonimo": false,
            "created_at": "2026-05-17T19:38:12.000000Z",
            "updated_at": "2026-05-17T19:38:12.000000Z",
            "cliente": {
                "id": 2,
                "nombre": "Emilio Ruiz",
                "correo": "22030128@itcelaya.edu.mx",
                "telefono": "4614103540",
                "carrera": "Ingeniería en Sistemas Computacionales",
                "latitud": null,
                "longitud": null,
                "negocio_activo": false,
                "baneado": false,
                "created_at": "2026-05-15T23:41:33.000000Z",
                "updated_at": "2026-05-15T23:41:33.000000Z"
            }
        }
    ],
    "message": "Reviews obtenidas correctamente."
}
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
//Validada
/*
* Respuesta 200
{
    "success": true,
    "data": [
        {
            "id": 2,
            "metodo": "Efectivo"
        },
        {
            "id": 1,
            "metodo": "Tarjeta"
        }
    ],
    "message": "Métodos de pago obtenidos correctamente."
}
*/
Route::get('/metodos-pago', [
    MetodoPagoController::class,
    'index'
]);
//validada
/*
* Respuesta 200
{
    "success": true,
    "data":
        {
            "id": 2,
            "metodo": "Efectivo"
        }

    "message": "Métodos de pago obtenidos correctamente."
}
*/
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
    //Validada
    Route::get('/me', [
        AuthController::class,
        'me'
    ]);

    //Validada
    Route::post('/logout', [
        AuthController::class,
        'logout'
    ]);

    /*
    |--------------------------------------------------------------------------
    | PRODUCTOS
    |--------------------------------------------------------------------------
    */
    //Validada
    /*
    * Parametros de Body:
    * {
        "nombre":
        "descripcion":
        "precio":
        "stock":
        "es_perecedero":
        "status":
        "categorias"
            [       {
                        "id":
                    }
        }
        "imagenes":
            [
                "https://site.com/1.jpg",
                "https://site.com/2.jpg"
            ]
    *Parametros de respuesta 201:
    {
        "success": true,
        "data": {
            "id_vendedor": 2,
            "nombre": "Galletas Principe",
            "descripcion": "Paquete con 12 galletas sabor chocolate.",
            "precio": "25.00",
            "stock": 25,
            "es_perecedero": true,
            "status": "disponible",
            "updated_at": "2026-05-17T06:07:56.000000Z",
            "created_at": "2026-05-17T06:07:56.000000Z",
            "id": 3,
            "vendedor": {
                "id": 2,
                "nombre": "Emilio Ruiz",
                "correo": "22030128@itcelaya.edu.mx",
                "telefono": "4614103540",
                "carrera": "Ingeniería en Sistemas Computacionales",
                "latitud": null,
                "longitud": null,
                "negocio_activo": false,
                "baneado": false,
                "created_at": "2026-05-15T23:41:33.000000Z",
                "updated_at": "2026-05-15T23:41:33.000000Z"
            },
            "categorias": [
                {
                    "id": 4,
                    "nombre": "Alimentos",
                    "pivot": {
                        "id_producto": 3,
                        "id_categoria": 4
                    }
                }
            ],
            "imagenes": [
                {
                    "id": 1,
                    "id_producto": 3,
                    "url_imagen": "https://site.com/1.jpg",
                    "orden": 1,
                    "created_at": "2026-05-17T06:07:56.000000Z",
                    "updated_at": "2026-05-17T06:07:56.000000Z"
                },
                {
                    "id": 2,
                    "id_producto": 3,
                    "url_imagen": "https://site.com/2.jpg",
                    "orden": 2,
                    "created_at": "2026-05-17T06:07:56.000000Z",
                    "updated_at": "2026-05-17T06:07:56.000000Z"
                }
            ]
        },
        "message": "Producto creado correctamente."
    }
    */
    Route::post('/productos', [
        ProductoController::class,
        'store'
    ])->middleware('role:vendedor');

    //Validada
    /*
    * Parametros de Body:
    * {
        "nombre":
        "descripcion":
        "precio":
        "stock":
        "es_perecedero":
        "status":
        "categorias"
            [       {
                        "id":
                    }
        }
        "imagenes":
            [
                "https://site.com/1.jpg",
                "https://site.com/2.jpg"
            ]
    * }
    */
    Route::put('/productos/{id}', [
        ProductoController::class,
        'update'
    ])->middleware('role:vendedor');

    //validada
    Route::delete('/productos/{id}', [
        ProductoController::class,
        'destroy'
    ])->middleware('role:vendedor');

    //validada
    Route::get('/mis-productos', [
        ProductoController::class,
        'misProductos'
    ])->middleware('role:vendedor');

    /*
    |--------------------------------------------------------------------------
    | CATEGORÍAS
    |--------------------------------------------------------------------------
    */

    //Validada
    /*
     * Parametros de Body:
     * {
            "nombre": "Dulces"
     * }
     * Respuesta 201:
     * {
            "success": true,
            "data": {
                    "nombre": "Dulces",
                    "id": 3
                    },
            "message": "Categoría creada correctamente."
     * }
     */
    Route::post('/categorias', [
        CategoriaController::class,
        'store'
    ])->middleware('role:admin');
    //validada
    /*
    * Parametros de Body:
    *{
        "nombre": "Frituras"
    *}
    * Respuesta 200:
        {
            "success": true,
            "data": {
                        "id": 3,
                        "nombre": "Frituras"
                    },
            "message": "Categoría actualizada correctamente."
        }
     */
    Route::put('/categorias/{id}', [
        CategoriaController::class,
        'update'
    ])->middleware('role:admin');

    //validada
    Route::delete('/categorias/{id}', [
        CategoriaController::class,
        'destroy'
    ])->middleware('role:admin');

    /*
    |--------------------------------------------------------------------------
    | IMÁGENES PRODUCTO
    |--------------------------------------------------------------------------
    */

    //Validada
    /*
    * Parametros de Body:
    {
        "success": true,
        "data": [],
        "message": "Imagen eliminada correctamente."
    */
    Route::delete('/imagenes-producto/{id}', [
        ImagenProductoController::class,
        'destroy'
    ])->middleware('role:vendedor');

    /*
    |--------------------------------------------------------------------------
    | VENTAS
    |--------------------------------------------------------------------------
    */

    //validada
    /*
    *response 200
    {
    "success": true,
    "data": [
        {
            "id": 9,
            "id_cliente": 2,
            "lugar": "UTICS",
            "latitud": null,
            "longitud": null,
            "status": "confirmada",
            "fecha": "2026-05-19T00:00:00.000000Z",
            "created_at": "2026-05-19T02:51:12.000000Z",
            "updated_at": "2026-05-19T02:51:12.000000Z",
            "cliente": {
                "id": 2,
                "nombre": "Emilio Ruiz",
                "correo": "22030128@itcelaya.edu.mx",
                "telefono": "4614103540",
                "carrera": "Ingeniería en Sistemas Computacionales",
                "latitud": null,
                "longitud": null,
                "negocio_activo": false,
                "baneado": false,
                "created_at": "2026-05-15T23:41:33.000000Z",
                "updated_at": "2026-05-15T23:41:33.000000Z"
            },
            "detalles": [
                {
                    "id": 8,
                    "cantidad": 1,
                    "precio_unitario": "10.00",
                    "id_venta": 9,
                    "id_producto": 3,
                    "created_at": "2026-05-19T02:51:13.000000Z",
                    "updated_at": "2026-05-19T02:51:13.000000Z",
                    "producto": {
                        "id": 3,
                        "id_vendedor": 2,
                        "nombre": "Galletas Emperador",
                        "descripcion": "Paquete con 4 galletas sabor vainilla.",
                        "precio": "10.00",
                        "stock": 3,
                        "es_perecedero": true,
                        "status": "disponible",
                        "created_at": "2026-05-17T06:07:56.000000Z",
                        "updated_at": "2026-05-19T02:51:13.000000Z",
                        "vendedor": {
                            "id": 2,
                            "nombre": "Emilio Ruiz",
                            "correo": "22030128@itcelaya.edu.mx",
                            "telefono": "4614103540",
                            "carrera": "Ingeniería en Sistemas Computacionales",
                            "latitud": null,
                            "longitud": null,
                            "negocio_activo": false,
                            "baneado": false,
                            "created_at": "2026-05-15T23:41:33.000000Z",
                            "updated_at": "2026-05-15T23:41:33.000000Z"
                        }
                    }
                }
            ],
            "transaccion": {
                "id_venta": 9,
                "consecutivo": 1,
                "total": "10.00",
                "id_metodo_de_pago": 1,
                "created_at": "2026-05-19T02:51:13.000000Z",
                "updated_at": "2026-05-19T02:51:13.000000Z",
                "id": 1,
                "metodo_pago": {
                    "id": 1,
                    "metodo": "Tarjeta de crédito o débito"
                }
            }
        },
     */
    Route::get('/ventas', [
        VentaController::class,
        'indexAdmin'
    ])->middleware('role:admin');

    //validada
    /*
    * Body:
    {
        "status":"confirmada/pagado/pendiente",
        "lugar":"",
        "fecha":""
    }
    * Response 200
    [
    {
            "id": 9,
            "id_cliente": 2,
            "lugar": "UTICS",
            "latitud": null,
            "longitud": null,
            "status": "confirmada",
            "fecha": "2026-05-19T00:00:00.000000Z",
            "created_at": "2026-05-19T02:51:12.000000Z",
            "updated_at": "2026-05-19T02:51:12.000000Z",
            "cliente": {
                "id": 2,
                "nombre": "Emilio Ruiz",
                "correo": "22030128@itcelaya.edu.mx",
                "telefono": "4614103540",
                "carrera": "Ingeniería en Sistemas Computacionales",
                "latitud": null,
                "longitud": null,
                "negocio_activo": false,
                "baneado": false,
                "created_at": "2026-05-15T23:41:33.000000Z",
                "updated_at": "2026-05-15T23:41:33.000000Z"
            },
            "detalles": [
                {
                    "id": 8,
                    "cantidad": 1,
                    "precio_unitario": "10.00",
                    "id_venta": 9,
                    "id_producto": 3,
                    "created_at": "2026-05-19T02:51:13.000000Z",
                    "updated_at": "2026-05-19T02:51:13.000000Z",
                    "producto": {
                        "id": 3,
                        "id_vendedor": 2,
                        "nombre": "Galletas Emperador",
                        "descripcion": "Paquete con 4 galletas sabor vainilla.",
                        "precio": "10.00",
                        "stock": 3,
                        "es_perecedero": true,
                        "status": "disponible",
                        "created_at": "2026-05-17T06:07:56.000000Z",
                        "updated_at": "2026-05-19T02:51:13.000000Z",
                        "vendedor": {
                            "id": 2,
                            "nombre": "Emilio Ruiz",
                            "correo": "22030128@itcelaya.edu.mx",
                            "telefono": "4614103540",
                            "carrera": "Ingeniería en Sistemas Computacionales",
                            "latitud": null,
                            "longitud": null,
                            "negocio_activo": false,
                            "baneado": false,
                            "created_at": "2026-05-15T23:41:33.000000Z",
                            "updated_at": "2026-05-15T23:41:33.000000Z"
                        }
                    }
                }
            ],
            "transaccion": {
                "id_venta": 9,
                "consecutivo": 1,
                "total": "10.00",
                "id_metodo_de_pago": 1,
                "created_at": "2026-05-19T02:51:13.000000Z",
                "updated_at": "2026-05-19T02:51:13.000000Z",
                "id": 1,
                "metodo_pago": {
                    "id": 1,
                    "metodo": "Tarjeta de crédito o débito"
                }
            }
        }
    ]
     */
    Route::post('/ventas/filtros', [
        VentaController::class,
        'indexAdminFiltros'
    ])->middleware('role:admin');

    //Validada
    //Es lo mismo que la anterior pero filtrando por vendedor
     Route::post('/mis-ventas/filtros', [
        VentaController::class,
        'misVentasFiltros'
    ])->middleware('role:vendedor');

    //Validada
    //Es lo mismo que la anterior pero filtrando por cliente
     Route::post('/mis-compras/filtros', [
        VentaController::class,
        'misComprasFiltros'
    ])->middleware('role:cliente');
    //validada
    /*
    * Repuesta 200
    *{
    "success": true,
    "data": [
        {
            "id": 9,
            "id_cliente": 2,
            "lugar": "UTICS",
            "latitud": null,
            "longitud": null,
            "status": "confirmada",
            "fecha": "2026-05-19T00:00:00.000000Z",
            "created_at": "2026-05-19T02:51:12.000000Z",
            "updated_at": "2026-05-19T02:51:12.000000Z",
            "cliente": {
                "id": 2,
                "nombre": "Emilio Ruiz",
                "correo": "22030128@itcelaya.edu.mx",
                "telefono": "4614103540",
                "carrera": "Ingeniería en Sistemas Computacionales",
                "latitud": null,
                "longitud": null,
                "negocio_activo": false,
                "baneado": false,
                "created_at": "2026-05-15T23:41:33.000000Z",
                "updated_at": "2026-05-15T23:41:33.000000Z"
            },
            "detalles": [
                {
                    "id": 8,
                    "cantidad": 1,
                    "precio_unitario": "10.00",
                    "id_venta": 9,
                    "id_producto": 3,
                    "created_at": "2026-05-19T02:51:13.000000Z",
                    "updated_at": "2026-05-19T02:51:13.000000Z",
                    "producto": {
                        "id": 3,
                        "id_vendedor": 2,
                        "nombre": "Galletas Emperador",
                        "descripcion": "Paquete con 4 galletas sabor vainilla.",
                        "precio": "10.00",
                        "stock": 3,
                        "es_perecedero": true,
                        "status": "disponible",
                        "created_at": "2026-05-17T06:07:56.000000Z",
                        "updated_at": "2026-05-19T02:51:13.000000Z",
                        "imagenes": [
                            {
                                "id": 3,
                                "id_producto": 3,
                                "url_imagen": "https://site.com/1.jpg",
                                "orden": 1,
                                "created_at": "2026-05-17T06:19:26.000000Z",
                                "updated_at": "2026-05-17T06:19:26.000000Z"
                            },
                            {
                                "id": 4,
                                "id_producto": 3,
                                "url_imagen": "https://site.com/2.jpg",
                                "orden": 2,
                                "created_at": "2026-05-17T06:19:26.000000Z",
                                "updated_at": "2026-05-17T06:19:26.000000Z"
                            }
                        ]
                    }
                }
            ],
            "transaccion": {
                "id_venta": 9,
                "consecutivo": 1,
                "total": "10.00",
                "id_metodo_de_pago": 1,
                "created_at": "2026-05-19T02:51:13.000000Z",
                "updated_at": "2026-05-19T02:51:13.000000Z",
                "id": 1,
                "metodo_pago": {
                    "id": 1,
                    "metodo": "Tarjeta de crédito o débito"
                }
            }
        },
    */
    Route::get('/mis-ventas', [
        VentaController::class,
        'misVentas'
    ])->middleware('role:vendedor');

    //validada
    /*
    *{
    "success": true,
    "data": [
        {
            "id": 9,
            "id_cliente": 2,
            "lugar": "UTICS",
            "latitud": null,
            "longitud": null,
            "status": "confirmada",
            "fecha": "2026-05-19T00:00:00.000000Z",
            "created_at": "2026-05-19T02:51:12.000000Z",
            "updated_at": "2026-05-19T02:51:12.000000Z",
            "detalles": [
                {
                    "id": 8,
                    "cantidad": 1,
                    "precio_unitario": "10.00",
                    "id_venta": 9,
                    "id_producto": 3,
                    "created_at": "2026-05-19T02:51:13.000000Z",
                    "updated_at": "2026-05-19T02:51:13.000000Z",
                    "producto": {
                        "id": 3,
                        "id_vendedor": 2,
                        "nombre": "Galletas Emperador",
                        "descripcion": "Paquete con 4 galletas sabor vainilla.",
                        "precio": "10.00",
                        "stock": 3,
                        "es_perecedero": true,
                        "status": "disponible",
                        "created_at": "2026-05-17T06:07:56.000000Z",
                        "updated_at": "2026-05-19T02:51:13.000000Z",
                        "vendedor": {
                            "id": 2,
                            "nombre": "Emilio Ruiz",
                            "correo": "22030128@itcelaya.edu.mx",
                            "telefono": "4614103540",
                            "carrera": "Ingeniería en Sistemas Computacionales",
                            "latitud": null,
                            "longitud": null,
                            "negocio_activo": false,
                            "baneado": false,
                            "created_at": "2026-05-15T23:41:33.000000Z",
                            "updated_at": "2026-05-15T23:41:33.000000Z"
                        }
                    }
                }
            ],
            "transaccion": {
                "id_venta": 9,
                "consecutivo": 1,
                "total": "10.00",
                "id_metodo_de_pago": 1,
                "created_at": "2026-05-19T02:51:13.000000Z",
                "updated_at": "2026-05-19T02:51:13.000000Z",
                "id": 1,
                "metodo_pago": {
                    "id": 1,
                    "metodo": "Tarjeta de crédito o débito"
                }
            }
        },

     */
    Route::get('/mis-compras', [
        VentaController::class,
        'misCompras'
    ])->middleware('role:cliente');

     //validada
    /*
    * Repuesta 200
    * {
        "success": true,
        "data": {
            "id": 2,
            "id_cliente": 2,
            "lugar": "UTICS",
            "latitud": null,
            "longitud": null,
            "status": "pendiente",
            "fecha": "2026-05-18T00:00:00.000000Z",
            "created_at": "2026-05-18T04:13:10.000000Z",
            "updated_at": "2026-05-18T04:13:10.000000Z",
            "cliente": {
                "id": 2,
                "nombre": "Emilio Ruiz",
                "correo": "22030128@itcelaya.edu.mx",
                "telefono": "4614103540",
                "carrera": "Ingeniería en Sistemas Computacionales",
                "latitud": null,
                "longitud": null,
                "negocio_activo": false,
                "baneado": false,
                "created_at": "2026-05-15T23:41:33.000000Z",
                "updated_at": "2026-05-15T23:41:33.000000Z"
            },
            "detalles": [
                {
                    "id": 1,
                    "cantidad": 1,
                    "precio_unitario": "10.00",
                    "id_venta": 2,
                    "id_producto": 3,
                    "created_at": "2026-05-18T04:13:10.000000Z",
                    "updated_at": "2026-05-18T04:13:10.000000Z",
                    "producto": {
                        "id": 3,
                        "id_vendedor": 2,
                        "nombre": "Galletas Emperador",
                        "descripcion": "Paquete con 4 galletas sabor vainilla.",
                        "precio": "10.00",
                        "stock": 3,
                        "es_perecedero": true,
                        "status": "disponible",
                        "created_at": "2026-05-17T06:07:56.000000Z",
                        "updated_at": "2026-05-19T02:51:13.000000Z",
                        "vendedor": {
                            "id": 2,
                            "nombre": "Emilio Ruiz",
                            "correo": "22030128@itcelaya.edu.mx",
                            "telefono": "4614103540",
                            "carrera": "Ingeniería en Sistemas Computacionales",
                            "latitud": null,
                            "longitud": null,
                            "negocio_activo": false,
                            "baneado": false,
                            "created_at": "2026-05-15T23:41:33.000000Z",
                            "updated_at": "2026-05-15T23:41:33.000000Z"
                        }
                    }
                }
            ],
            "transaccion": null
        },
        "message": "Venta obtenida correctamente."
    }
     */
    Route::get('/ventas/{id}', [
        VentaController::class,
        'show'
    ])->middleware('role:cliente,vendedor');

    //validada
    /**
     * Parametros de Body:
     * {
     *       "lugar": "UTICS",
     *       "tipo": "pagada",
     *       "productos" :
     *       [
     *           {
     *               "id_producto": 3,
     *               "cantidad": 1
     *           }
     *       ],
     *       "id_metodo_de_pago":1
     * }
     * Se puede mandar sin el campo "id_metodo_de_pago" si el tipo es "pendiente".
     *
     * Respuesta 201:
     * { "success": true,
     * "data": {
     *      "id_cliente": 2,
     *       "lugar": "UTICS",
     *       "latitud": null,
     *       "longitud": null,
     *       "status": "confirmada",
     *       "fecha": "2026-05-19T02:51:12.000000Z",
     *       "updated_at": "2026-05-19T02:51:12.000000Z",
     *       "created_at": "2026-05-19T02:51:12.000000Z",
     *       "id": 9,
     *       "cliente": {
     *           "id": 2,
     *           "nombre": "Emilio Ruiz",
     *           "correo": "22030128@itcelaya.edu.mx",
     *           "telefono": "4614103540",
     *           "carrera": "Ingeniería en Sistemas Computacionales",
     *           "latitud": null,
     *           "longitud": null,
     *           "negocio_activo": false,
     *           "baneado": false,
     *           "created_at": "2026-05-15T23:41:33.000000Z",
     *           "updated_at": "2026-05-15T23:41:33.000000Z"
     *       },
     *       "detalles": [
     *           {
     *               "id": 8,
     *               "cantidad": 1,
     *               "precio_unitario": "10.00",
     *               "id_venta": 9,
     *               "id_producto": 3,
     *               "created_at": "2026-05-19T02:51:13.000000Z",
     *               "updated_at": "2026-05-19T02:51:13.000000Z",
     *               "producto": {
     *                   "id": 3,
     *                   "id_vendedor": 2,
     *                   "nombre": "Galletas Emperador",
     *                   "descripcion": "Paquete con 4 galletas sabor vainilla.",
     *                   "precio": "10.00",
     *                   "stock": 3,
     *                   "es_perecedero": true,
     *                   "status": "disponible",
     *                   "created_at": "2026-05-17T06:07:56.000000Z",
     *                   "updated_at": "2026-05-19T02:51:13.000000Z",
     *                   "vendedor": {
     *                       "id": 2,
     *                       "nombre": "Emilio Ruiz",
     *                       "correo": "22030128@itcelaya.edu.mx",
     *                       "telefono": "4614103540",
     *                       "carrera": "Ingeniería en Sistemas Computacionales",
     *                       "latitud": null,
     *                       "longitud": null,
     *                       "negocio_activo": false,
     *                       "baneado": false,
     *                       "created_at": "2026-05-15T23:41:33.000000Z",
     *                       "updated_at": "2026-05-15T23:41:33.000000Z"
     *                   }
     *               }
     *           }
     *       ],
     *       "transaccion": {
     *           "id_venta": 9,
     *           "consecutivo": 1,
     *           "total": "10.00",
     *           "id_metodo_de_pago": 1,
     *           "created_at": "2026-05-19T02:51:13.000000Z",
     *           "updated_at": "2026-05-19T02:51:13.000000Z",
     *           "id": 1,
     *           "metodo_pago": {
     *               "id": 1,
     *               "metodo": "Tarjeta de crédito o débito"
     *           }
     *       }
     *   },
     *   "message": "Venta creada correctamente."
     *}
     */
    Route::post('/ventas', [
        VentaController::class,
        'store'
    ])->middleware('role:cliente');


    //validada
    /*
    * Body:
    {
        "id_metodo_de_pago": 1
    }
    * Response 200
    {
        "success": true,
        "data": {
            "id": 3,
            "id_cliente": 2,
            "lugar": "UTICS",
            "latitud": null,
            "longitud": null,
            "status": "confirmada",
            "fecha": "2026-05-18T00:00:00.000000Z",
            "created_at": "2026-05-18T04:15:29.000000Z",
            "updated_at": "2026-05-19T05:06:52.000000Z",
            "detalles": [
                {
                    "id": 2,
                    "cantidad": 1,
                    "precio_unitario": "10.00",
                    "id_venta": 3,
                    "id_producto": 3,
                    "created_at": "2026-05-18T04:15:29.000000Z",
                    "updated_at": "2026-05-18T04:15:29.000000Z",
                    "producto": {
                        "id": 3,
                        "id_vendedor": 2,
                        "nombre": "Galletas Emperador",
                        "descripcion": "Paquete con 4 galletas sabor vainilla.",
                        "precio": "10.00",
                        "stock": 3,
                        "es_perecedero": true,
                        "status": "disponible",
                        "created_at": "2026-05-17T06:07:56.000000Z",
                        "updated_at": "2026-05-19T02:51:13.000000Z"
                    }
                }
            ],
            "cliente": {
                "id": 2,
                "nombre": "Emilio Ruiz",
                "correo": "22030128@itcelaya.edu.mx",
                "telefono": "4614103540",
                "carrera": "Ingeniería en Sistemas Computacionales",
                "latitud": null,
                "longitud": null,
                "negocio_activo": false,
                "baneado": false,
                "created_at": "2026-05-15T23:41:33.000000Z",
                "updated_at": "2026-05-15T23:41:33.000000Z"
            },
            "transaccion": {
                "id_venta": 3,
                "consecutivo": 1,
                "total": "10.00",
                "id_metodo_de_pago": 1,
                "created_at": "2026-05-19T05:06:52.000000Z",
                "updated_at": "2026-05-19T05:06:52.000000Z",
                "id": 3,
                "metodo_pago": {
                    "id": 1,
                    "metodo": "Tarjeta de crédito o débito"
                }
            }
        },
        "message": "Venta pagada correctamente."
    }
    */
    Route::post('/ventas/{id}/pagar', [
        VentaController::class,
        'pagar'
    ])->middleware('role:cliente');

    //validada
    /*
    * Response 200
    {
        "success": true,
        "data": {
            "id": 4,
            "id_cliente": 2,
            "lugar": "UTICS",
            "latitud": null,
            "longitud": null,
            "status": "cancelada",
            "fecha": "2026-05-18T00:00:00.000000Z",
            "created_at": "2026-05-18T04:16:38.000000Z",
            "updated_at": "2026-05-19T05:13:18.000000Z"
        },
        "message": "Venta cancelada correctamente."
    }
     */
    Route::put('/ventas/{id}/cancelar', [
        VentaController::class,
        'cancelar'
    ])->middleware('role:cliente,vendedor,admin');

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
    //Validada
    /*
    * Parametros de Body:
    {
        "id_producto": ,
        "calificacion": ,
        "comentario": "",
        "anonimo": true/false
    }
    * Respuesta 201:
    {
        "success": true,
        "data": {
                    "id_cliente": 2,
                    "id_producto": 3,
                    "calificacion": 4,
                    "comentario": "Esta en buen estado, solo que tardó más de lo esperado",
                    "estado": "activa",
                    "anonimo": false,
                    "updated_at": "2026-05-17T19:38:12.000000Z",
                    "created_at": "2026-05-17T19:38:12.000000Z",
                    "id": 1,
                    "cliente": {
                                "id": 2,
                                "nombre": "Emilio Ruiz",
                                "correo": "22030128@itcelaya.edu.mx",
                                "telefono": "4614103540",
                                "carrera": "Ingeniería en Sistemas Computacionales",
                                "latitud": null,
                                "longitud": null,
                                "negocio_activo": false,
                                "baneado": false,
                                "created_at": "2026-05-15T23:41:33.000000Z",
                                "updated_at": "2026-05-15T23:41:33.000000Z"
                                },
                    "producto": {
                                "id": 3,
                                "id_vendedor": 2,
                                "nombre": "Galletas Emperador",
                                "descripcion": "Paquete con 4 galletas sabor vainilla.",
                                "precio": "10.00",
                                "stock": 5,
                                "es_perecedero": true,
                                "status": "disponible",
                                "created_at": "2026-05-17T06:07:56.000000Z",
                                "updated_at": "2026-05-17T06:19:26.000000Z"
                                }
                            },
                    "message": "Review creada correctamente."
                }
     */
    Route::post('/reviews/productos', [
        ReviewProductoController::class,
        'store'
    ])->middleware('role:cliente');

//validada
/*
* Respuesta 200
{
    "success": true,
    "data": {
        "id": 1,
        "id_cliente": 2,
        "id_producto": 3,
        "calificacion": 4,
        "comentario": "Esta en buen estado, solo que tardó más de lo esperado",
        "estado": "activa",
        "anonimo": false,
        "created_at": "2026-05-17T19:38:12.000000Z",
        "updated_at": "2026-05-17T19:38:12.000000Z",
        "cliente": {
            "id": 2,
            "nombre": "Emilio Ruiz",
            "correo": "22030128@itcelaya.edu.mx",
            "telefono": "4614103540",
            "carrera": "Ingeniería en Sistemas Computacionales",
            "latitud": null,
            "longitud": null,
            "negocio_activo": false,
            "baneado": false,
            "created_at": "2026-05-15T23:41:33.000000Z",
            "updated_at": "2026-05-15T23:41:33.000000Z"
        },
        "producto": {
            "id": 3,
            "id_vendedor": 2,
            "nombre": "Galletas Emperador",
            "descripcion": "Paquete con 4 galletas sabor vainilla.",
            "precio": "10.00",
            "stock": 5,
            "es_perecedero": true,
            "status": "disponible",
            "created_at": "2026-05-17T06:07:56.000000Z",
            "updated_at": "2026-05-17T06:19:26.000000Z"
        }
    },
    "message": "Review obtenida correctamente."
}
*/
    Route::get('/reviews/productos/show/{id}', [
        ReviewProductoController::class,
        'show'
    ])->middleware('role:cliente,vendedor,admin');

    //validada
    /*
    * Parametros de Body:
    {
            "calificacion": ,
            "comentario": "",
            "estado": "activa/oculta/eliminada",
            "anonimo": true/false
        }
        * Respuesta 200:
        {
        "success": true,
        "data": {
            "id": 1,
            "id_cliente": 2,
            "id_producto": 3,
            "calificacion": 4,
            "comentario": "Esta en buen estado, solo que tardó más de lo esperado",
            "estado": "activa",
            "anonimo": false,
            "created_at": "2026-05-17T19:38:12.000000Z",
            "updated_at": "2026-05-17T19:38:12.000000Z",
            "cliente": {
                "id": 2,
                "nombre": "Emilio Ruiz",
                "correo": "22030128@itcelaya.edu.mx",
                "telefono": "4614103540",
                "carrera": "Ingeniería en Sistemas Computacionales",
                "latitud": null,
                "longitud": null,
                "negocio_activo": false,
                "baneado": false,
                "created_at": "2026-05-15T23:41:33.000000Z",
                "updated_at": "2026-05-15T23:41:33.000000Z"
            },
            "producto": {
                "id": 3,
                "id_vendedor": 2,
                "nombre": "Galletas Emperador",
                "descripcion": "Paquete con 4 galletas sabor vainilla.",
                "precio": "10.00",
                "stock": 5,
                "es_perecedero": true,
                "status": "disponible",
                "created_at": "2026-05-17T06:07:56.000000Z",
                "updated_at": "2026-05-17T06:19:26.000000Z"
            }
        },
        "message": "Review actualizada correctamente."
    }
    */
    Route::put('/reviews/productos/{id}', [
        ReviewProductoController::class,
        'update'
    ])->middleware('role:cliente,admin');

    //validada
     /*
     * Respuesta 200
     {
        "success": true,
        "data": [],
        "message": "Review eliminada correctamente."
     }
     */
    Route::delete('/reviews/productos/{id}', [
        ReviewProductoController::class,
        'destroy'
    ])->middleware('role:cliente,admin');


    //validada
    /*
    * Respuesta 200
    {
        "success": true,
        "data": [
            {
                "id": 2,
                "id_cliente": 2,
                "id_producto": 3,
                "calificacion": 4,
                "comentario": "Esta en buen estado, solo que tardó más de lo esperado",
                "estado": "activa",
                "anonimo": false,
                "created_at": "2026-05-18T03:41:03.000000Z",
                "updated_at": "2026-05-18T03:41:03.000000Z",
                "producto": {
                    "id": 3,
                    "id_vendedor": 2,
                    "nombre": "Galletas Emperador",
                    "descripcion": "Paquete con 4 galletas sabor vainilla.",
                    "precio": "10.00",
                    "stock": 5,
                    "es_perecedero": true,
                    "status": "disponible",
                    "created_at": "2026-05-17T06:07:56.000000Z",
                    "updated_at": "2026-05-17T06:19:26.000000Z",
                    "imagenes": [
                        {
                            "id": 3,
                            "id_producto": 3,
                            "url_imagen": "https://site.com/1.jpg",
                            "orden": 1,
                            "created_at": "2026-05-17T06:19:26.000000Z",
                            "updated_at": "2026-05-17T06:19:26.000000Z"
                        },
                        {
                            "id": 4,
                            "id_producto": 3,
                            "url_imagen": "https://site.com/2.jpg",
                            "orden": 2,
                            "created_at": "2026-05-17T06:19:26.000000Z",
                            "updated_at": "2026-05-17T06:19:26.000000Z"
                        }
                    ]
                }
            }
        ],
        "message": "Reviews del usuario obtenidas correctamente."
    }
     */
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

    //validada
     /*
     * Parametros de Body:
     * {
            "metodo": "Transferencia"
     * }
     * Respuesta 201:
     * {
            "success": true,
            "data": {
                    "metodo": "Transferencia",
                    "id": 3
                    },
            "message": "Método de pago creado correctamente."
     * }
     */
    Route::post('/metodos-pago', [
        MetodoPagoController::class,
        'store'
    ])->middleware('role:admin');

    //validada
    /*
    * Parametros de Body:
    *{
        "metodo": "Pago móvil"
    *}
    * Respuesta 200:
    {
        "success": true,
        "data": {
                    "id": 3,
                    "metodo": "Pago móvil"
                },
        "message": "Método de pago actualizado correctamente."
    }
     */
    Route::put('/metodos-pago/{id}', [
        MetodoPagoController::class,
        'update'
    ])->middleware('role:admin');

    //validada
    Route::delete('/metodos-pago/{id}', [
        MetodoPagoController::class,
        'destroy'
    ])->middleware('role:admin');
});
