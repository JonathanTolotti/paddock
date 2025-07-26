<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class Vehicle extends Model
{
    use HasFactory;

    protected $fillable = [
        'client_id',
        'plate',
        'make',
        'model',
        'year',
        'color',
        'vin',
        'notes',
    ];

    /**
     * Gera um UUID automaticamente.
     */
    protected static function booted(): void
    {
        static::creating(function (Vehicle $vehicle) {
            if (empty($vehicle->uuid)) {
                $vehicle->uuid = Str::uuid();
            }
        });
    }

    /**
     * Define que as rotas usarão 'uuid'.
     */
    public function getRouteKeyName(): string
    {
        return 'uuid';
    }

    /**
     * Define a relação: um Veículo pertence a um Cliente.
     */
    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }
}
