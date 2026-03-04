<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;


class Table extends Model
{
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'location',
        'status',
        'occupied_at',
        'is_domicilio',
        'cliente_nombre',
        'cliente_telefono',
        'cliente_direccion',
    ];

    protected $casts = [
        'occupied_at' => 'datetime',
        'is_domicilio' => 'boolean',
    ];

    public function elementTables()
    {
        return $this->hasMany(ElementTable::class, 'table_id');
    }

    public function scopeDomicilios($query)
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
