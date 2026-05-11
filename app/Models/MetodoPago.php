<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class MetodoPago extends Model
{
    use HasFactory;

    public $timestamps = false;
    protected $table = 'metodo_pago';
    protected $primaryKey = 'id';

    protected $fillable = [
        'metodo'
    ];

    public function transacciones()
    {
        return $this->hasMany(Transaccion::class, 'id_metodo_de_pago', 'id');
    }
}
