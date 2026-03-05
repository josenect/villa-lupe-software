<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;


class Table extends Model
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'location',
        'status',
        'occupied_at',
        'is_domicilio',
    ];

    protected $casts = [
        'occupied_at' => 'datetime',
        'is_domicilio' => 'boolean',
    ];

    public function elementTables()
    {
        return $this->hasMany(ElementTable::class, 'table_id');
    }

    public function facturas()
    {
        return $this->hasMany(Factura::class, 'table_id');
    }

    public function domicilio()
    {
        return $this->hasOne(Domicilio::class, 'table_id')->where('estado', Domicilio::ESTADO_ACTIVO);
    }

    public function domicilios()
    {
        return $this->hasMany(Domicilio::class, 'table_id');
    }

    public function scopeDomicilioSlots($query)
    {
        return $query->where('is_domicilio', true);
    }

    public function scopeMesasFisicas($query)
    {
        return $query->where('is_domicilio', false);
    }

    public function esDomicilio()
    {
        return $this->is_domicilio;
    }
}
