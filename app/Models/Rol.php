<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Rol extends Model
{
    use HasFactory;

    public $timestamps = false;
    protected $table = 'roles';
    protected $primaryKey = 'id';

    protected $fillable = [
        'nombre'
    ];

    public function usuarios()
    {
        return $this->belongsToMany(User::class, 'usuario_roles', 'id_rol', 'id_usuario');
    }
}
