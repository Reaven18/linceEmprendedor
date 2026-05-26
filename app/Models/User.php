<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $table = 'usuarios';
    protected $primaryKey = 'id';

    protected $fillable = [
        'nombre',
        'correo',
        'password',
        'telefono',
        'carrera',
        'latitud',
        'longitud',
        'negocio_activo',
        'baneado',
        'url'        
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    public function roles()
    {
        return $this->belongsToMany(Rol::class, 'usuario_roles', 'id_usuario', 'id_rol');
    }

    public function reviewsVendedor()
    {
        return $this->hasMany(ReviewVendedores::class, 'id_vendedor', 'id');
    }

    public function reviewsCliente()
    {
        return $this->hasMany(ReviewVendedores::class, 'id_cliente', 'id');
    }

    public function reviewsProducto()
    {
        return $this->hasMany(ReviewProductos::class, 'id_cliente', 'id');
    }

    public function productos()
    {
        return $this->hasMany(Producto::class, 'id_vendedor', 'id');
    }
}
