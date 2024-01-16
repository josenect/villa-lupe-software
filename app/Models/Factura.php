<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Factura extends Model
{
    protected $table = 'facturas';

    protected $fillable = [
        'table_id',
        'numero_factura',
        'valor_total',
        'valor_propina',
        'valor_pagado',
        'fecha_hora_factura',
        'medio_pago',
    ];

    // Relación con la tabla DetalleFactura
    public function detalleFacturas()
    {
        return $this->hasMany(DetalleFactura::class);
    }
}