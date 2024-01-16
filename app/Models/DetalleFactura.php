<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DetalleFactura extends Model
{
    protected $table = 'detalle_facturas';

    protected $fillable = [
        'table_id',
        'factura_id',
        'producto_id',
        'price',
        'amount',
        'discount',
        'record',
    ];

    // Relación con la tabla Factura
    public function factura()
    {
        return $this->belongsTo(Factura::class);
    }

    public function producto()
    {
        return $this->belongsTo(Producto::class, 'producto_id', 'id');
    }
}
