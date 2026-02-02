<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LogImpresion extends Model
{
    use HasFactory;

    protected $table = 'log_impresiones';

    protected $fillable = [
        'user_id',
        'table_id',
        'tipo',
    ];

    // Relación con usuario
    public function usuario()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    // Relación con mesa
    public function mesa()
    {
        return $this->belongsTo(Table::class, 'table_id', 'id');
    }
}
