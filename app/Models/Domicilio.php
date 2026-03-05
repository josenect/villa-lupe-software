<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Domicilio extends Model
{
    protected $table = 'domicilios';

    protected $fillable = [
        'table_id',
        'cliente_nombre',
        'cliente_telefono',
        'cliente_direccion',
        'estado',
    ];

    const ESTADO_ACTIVO    = 'activo';
    const ESTADO_FACTURADO = 'facturado';
    const ESTADO_CANCELADO = 'cancelado';

    public function mesa()
    {
        return $this->belongsTo(Table::class, 'table_id');
    }

    public function scopeActivos($query)
    {
        return $query->where('estado', self::ESTADO_ACTIVO);
    }

    public function scopeFacturados($query)
    {
        return $query->where('estado', self::ESTADO_FACTURADO);
    }

    public function scopeCancelados($query)
    {
        return $query->where('estado', self::ESTADO_CANCELADO);
    }

    public function estaActivo()
    {
        return $this->estado === self::ESTADO_ACTIVO;
    }
}
