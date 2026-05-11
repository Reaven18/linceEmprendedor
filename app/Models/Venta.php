<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Venta extends Model
{
    use HasFactory;

    protected $table = 'venta';
    protected $primaryKey = 'id';

    protected $fillable = [
        'id_cliente',
        'id_vendedor',
        'lugar',
        'latitud',
        'longitud',
        'status',
        'fecha',
    ];
    protected $casts = [
        'fecha' => 'datetime',
        'latitud' => 'float',
        'longitud' => 'float',
    ];

    public function cliente()
    {
        return $this->belongsTo(User::class, 'id_cliente', 'id');
    }

    public function vendedor()
    {
        return $this->belongsTo(User::class, 'id_vendedor', 'id');
    }
    public function detalles()
    {
        return $this->hasMany(VentaDetalle::class, 'id_venta', 'id');
    }
    public function transaccion()
    {
        return $this->hasOne(Transaccion::class, 'id_venta', 'id');
    }
}
