<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Mensaje extends Model
{
    protected $table = 'mensajes';

    protected $fillable = [
        'id_emisor',
        'id_receptor',
        'contenido',
        'leido',
        'enviado_en'
    ];

    public $timestamps = false;

    protected $casts = [
        'leido' => 'boolean',
        'enviado_en' => 'datetime',
    ];

    public function emisor()
    {
        return $this->belongsTo(User::class, 'id_emisor');
    }

    public function receptor()
    {
        return $this->belongsTo(User::class, 'id_receptor');
    }
}
