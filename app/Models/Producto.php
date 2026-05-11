<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Producto extends Model
{
    use HasFactory;

    protected $table = 'productos';
    protected $primaryKey = 'id';

    protected $fillable = [
        'id_vendedor',
        'nombre',
        'descripcion',
        'precio',
        'stock',
        'es_perecedero',
        'status'
    ];

    protected $casts = [
        'precio' => 'decimal:2',
        'stock' => 'integer',
        'es_perecedero' => 'boolean',
        'status' => 'string'
    ];

    public function scopeDisponible($query)
    {
        return $query->where('status', 'disponible');
    }

    public function scopeConStock($query)
    {
        return $query->where('stock', '>', 0);
    }

    public function vendedor()
    {
        return $this->belongsTo(User::class, 'id_vendedor', 'id');
    }

    public function categorias()
    {
        return $this->belongsToMany(Categoria::class, 'producto_categorias', 'id_producto', 'id_categoria');
    }

    public function imagenes()
    {
        return $this->hasMany(ImagenProducto::class, 'id_producto', 'id');
    }

    public function reviews()
    {
        return $this->hasMany(ReviewProductos::class, 'id_producto', 'id');
    }
}
