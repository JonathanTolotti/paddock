<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Client extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'name',
        'document_type',
        'document_number',
        'email',
        'phone_number',
        'address_street',
        'address_number',
        'address_complement',
        'address_neighborhood',
        'address_city',
        'address_state',
        'address_zip_code',
        'notes',
    ];

    /**
     * Define a relação: um Cliente pode ter muitos Veículos.
     */
    public function vehicles(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Vehicle::class);
    }

    /**
     * Gera um UUID automaticamente ao criar um novo cliente.
     */
    protected static function booted(): void
    {
        static::creating(function (Client $client) {
            if (empty($client->uuid)) {
                $client->uuid = Str::uuid();
            }
        });
    }

    /**
     * Define que as rotas usarão o 'uuid' em vez do 'id'.
     */
    public function getRouteKeyName(): string
    {
        return 'uuid';
    }
}
