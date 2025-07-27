<?php

namespace App\Models;

use App\Enums\WorkOrderStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Casts\Attribute;

class WorkOrder extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'client_id',
        'vehicle_id',
        'user_id',
        'status',
        'problem_reported',
        'technical_diagnosis',
        'total_parts_price',
        'total_services_price',
        'total_price',
        'finished_at',
    ];

    protected $casts = [
        'total_parts_price' => 'decimal:2',
        'total_services_price' => 'decimal:2',
        'total_price' => 'decimal:2',
        'finished_at' => 'datetime',
    ];

    protected function status(): Attribute
    {
        return Attribute::make(
            get: function ($value) {
                if (!$value) {
                    return null;
                }

                return [
                    'value' => $value,
                    'label' => WorkOrderStatus::from($value)->label(),
                    'color' => WorkOrderStatus::from($value)->color(),
                ];
            }
        );
    }

    protected static function booted(): void
    {
        static::creating(function (WorkOrder $workOrder) {
            if (empty($workOrder->uuid)) {
                $workOrder->uuid = Str::uuid();
            }
        });
    }

    public function getRouteKeyName(): string
    {
        return 'uuid';
    }

    // --- Relacionamentos ---

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    public function vehicle(): BelongsTo
    {
        return $this->belongsTo(Vehicle::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function parts(): BelongsToMany
    {
        return $this->belongsToMany(Part::class)
            ->withPivot('quantity', 'sale_price')
            ->withTimestamps();
    }

    public function services(): BelongsToMany
    {
        return $this->belongsToMany(Service::class)
            ->withPivot('price')
            ->withTimestamps();
    }
}
