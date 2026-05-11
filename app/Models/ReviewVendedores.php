<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ReviewVendedores extends Model
{
    use HasFactory;

    protected $table = 'reviewsVendedores';
    protected $primaryKey = 'id';

    protected $fillable = [
        'id_cliente',
        'id_vendedor',
        'calificacion',
        'comentario',
        'estado',
        'anonimo'
    ];

    protected $casts = [
        'calificacion' => 'integer',
        'anonimo' => 'boolean',
        'estado' => 'string',
    ];

    public function cliente()
    {
        return $this->belongsTo(User::class, 'id_cliente', 'id');
    }

    public function vendedor()
    {
        return $this->belongsTo(User::class, 'id_vendedor', 'id');
    }
}
