<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transaccion extends Model
{
    use HasFactory;

    protected $table = 'transaccion';
    protected $primaryKey = 'id';

    protected $fillable = [
        'id_venta',
        'consecutivo',
        'total',
        'id_metodo_de_pago'
    ];
    protected $casts = [
        'total' => 'decimal:2',
    ];

    public function metodoPago()
    {
        return $this->belongsTo(MetodoPago::class, 'id_metodo_de_pago', 'id');
    }

    public function venta()
    {
        return $this->belongsTo(Venta::class, 'id_venta', 'id');
    }
}
