<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    // Constantes de roles
    const ROL_ADMIN = 'admin';
    const ROL_MESERO = 'mesero';
    const ROL_COCINA = 'cocina';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'rol',
        'activo',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'activo' => 'boolean',
    ];

    // Verificar si es admin
    public function esAdmin()
    {
        return $this->rol === self::ROL_ADMIN;
    }

    // Verificar si es mesero
    public function esMesero()
    {
        return $this->rol === self::ROL_MESERO;
    }

    // Verificar si es cocina
    public function esCocina()
    {
        return $this->rol === self::ROL_COCINA;
    }

    // Relación con facturas generadas
    public function facturas()
    {
        return $this->hasMany(Factura::class);
    }

    // Relación con log de impresiones
    public function logImpresiones()
    {
        return $this->hasMany(LogImpresion::class);
    }

    // Scope para usuarios activos
    public function scopeActivos($query)
    {
        return $query->where('activo', true);
    }
}
