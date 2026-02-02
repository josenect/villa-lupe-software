<?php

namespace App\Models;

use App\Models\Producto;
use App\Models\User;
use App\Models\Table;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;


class ElementTable extends Model
{
    use HasFactory, Notifiable;

    // Constantes de estados
    const ESTADO_PENDIENTE = 'pendiente';
    const ESTADO_EN_COCINA = 'en_cocina';
    const ESTADO_LISTO = 'listo';
    const ESTADO_CANCELACION_SOLICITADA = 'cancelacion_solicitada';
    const ESTADO_CANCELADO = 'cancelado';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'table_id',
        'producto_id',
        'price',
        'amount',
        'dicount',
        'record',
        'status',
        'estado',
        'observacion',
        'motivo_cancelacion',
        'solicitado_por',
        'fecha_solicitud_cancelacion',
        'aprobado_por',
        'fecha_cancelacion',
        'user_id'
    ];

    protected $casts = [
        'fecha_solicitud_cancelacion' => 'datetime',
        'fecha_cancelacion' => 'datetime',
    ];

    public function producto()
    {
        return $this->belongsTo(Producto::class, 'producto_id', 'id');
    }

    public function mesa()
    {
        return $this->belongsTo(Table::class, 'table_id', 'id');
    }

    // Usuario que agregó el producto
    public function usuario()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    // Usuario que solicitó cancelación
    public function solicitadoPor()
    {
        return $this->belongsTo(User::class, 'solicitado_por', 'id');
    }

    // Usuario que aprobó cancelación
    public function aprobadoPor()
    {
        return $this->belongsTo(User::class, 'aprobado_por', 'id');
    }

    // Scope para productos activos (no cancelados)
    public function scopeActivos($query)
    {
        return $query->where('status', 1)->where('estado', '!=', self::ESTADO_CANCELADO);
    }

    // Scope para productos pendientes de cancelación
    public function scopePendientesCancelacion($query)
    {
        return $query->where('estado', self::ESTADO_CANCELACION_SOLICITADA);
    }

    // Scope para cocina (pendientes y en cocina)
    public function scopeParaCocina($query)
    {
        return $query->where('status', 1)
                     ->whereIn('estado', [self::ESTADO_PENDIENTE, self::ESTADO_EN_COCINA])
                     ->where('estado', '!=', self::ESTADO_CANCELADO);
    }

    // Verificar si está pendiente de cancelación
    public function estaPendienteCancelacion()
    {
        return $this->estado === self::ESTADO_CANCELACION_SOLICITADA;
    }

    // Verificar si está cancelado
    public function estaCancelado()
    {
        return $this->estado === self::ESTADO_CANCELADO;
    }
}
