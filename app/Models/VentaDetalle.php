<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class VentaDetalle extends Model
{
    use HasFactory;

    protected $table = 'venta_detalle';
    protected $primaryKey = 'id';

    protected $fillable = [
        'cantidad',
        'precio_unitario',
        'id_venta',
        'id_producto',
    ];

    public function venta()
    {
        return $this->belongsTo(Venta::class, 'id_venta', 'id');
    }
    public function producto()
    {
        return $this->belongsTo(Producto::class, 'id_producto', 'id');
    }
    public function vendedor()
    {
        return $this->hasOneThrough(
            User::class,
            Producto::class,
            'id',            
            'id',
            'id_producto',
            'id_vendedor'
        );
    }
}
