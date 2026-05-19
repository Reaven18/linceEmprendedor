<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ReviewProductos extends Model
{
    use HasFactory;

    protected $table = 'reviews_productos';
    protected $primaryKey = 'id';

    protected $fillable = [
        'id_cliente',
        'id_producto',
        'calificacion',
        'comentario',
        'estado',
        'anonimo'
    ];
    protected $casts = [
        'calificacion' => 'integer',
        'anonimo' => 'boolean',
    ];

    public function cliente()
    {
        return $this->belongsTo(User::class, 'id_cliente', 'id');
    }

    public function producto()
    {
        return $this->belongsTo(Producto::class, 'id_producto', 'id');
    }
}
