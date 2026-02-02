<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Factura extends Model
{
    protected $table = 'facturas';

    protected $fillable = [
        'table_id',
        'user_id',
        'numero_factura',
        'valor_total',
        'valor_propina',
        'valor_pagado',
        'valor_efectivo',
        'valor_transferencia',
        'fecha_hora_factura',
        'medio_pago',
        'estado',
        'motivo_anulacion',
        'fecha_anulacion',
    ];

    // Constantes para estados
    const ESTADO_ACTIVA = 'activa';
    const ESTADO_ANULADA = 'anulada';
    const ESTADO_REABIERTA = 'reabierta';

    // Relación con la tabla DetalleFactura
    public function detalleFacturas()
    {
        return $this->hasMany(DetalleFactura::class);
    }

    // Relación con la mesa
    public function mesa()
    {
        return $this->belongsTo(Table::class, 'table_id');
    }

    // Relación con el usuario que generó la factura
    public function usuario()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    // Verificar si la factura está activa
    public function estaActiva()
    {
        return $this->estado === self::ESTADO_ACTIVA;
    }

    // Verificar si la factura está anulada
    public function estaAnulada()
    {
        return $this->estado === self::ESTADO_ANULADA;
    }

    // Scope para facturas activas
    public function scopeActivas($query)
    {
        return $query->where('estado', self::ESTADO_ACTIVA);
    }

    // Scope para facturas anuladas
    public function scopeAnuladas($query)
    {
        return $query->where('estado', self::ESTADO_ANULADA);
    }
}
