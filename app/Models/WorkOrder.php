<?php

namespace App\Models;

use App\Enums\WorkOrderStatus;
use App\Observers\WorkOrderObserver;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Casts\Attribute;

#[ObservedBy(WorkOrderObserver::class)]
class WorkOrder extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'client_id',
        'vehicle_id',
        'user_id',
        'mechanic_id',
        'status',
        'problem_reported',
        'technical_diagnosis',
        'total_parts_price',
        'total_services_price',
        'total_price',
        'signed_url',
        'finished_at',
    ];

    protected $casts = [
        'total_parts_price' => 'decimal:2',
        'total_services_price' => 'decimal:2',
        'total_price' => 'decimal:2',
        'finished_at' => 'datetime',
        'status' => WorkOrderStatus::class,
    ];

    protected $appends = [
        'status_label',
        'status_color',
    ];

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

    public function mechanic(): BelongsTo
    {
        return $this->belongsTo(User::class, 'mechanic_id');
    }

    public function parts(): BelongsToMany
    {
        return $this->belongsToMany(Part::class)
            ->using(PartWorkOrder::class)
            ->withPivot('quantity', 'sale_price')
            ->withTimestamps();
    }

    public function services(): BelongsToMany
    {
        return $this->belongsToMany(Service::class)
            ->withPivot('price')
            ->withTimestamps();
    }

    public function statusHistories(): HasMany
    {
        return $this->hasMany(WorkOrderStatusHistory::class)->latest();
    }

    /**
     * Getter para o label do status.
     */
    public function getStatusLabelAttribute(): string
    {
        return $this->status->label();
    }

    /**
     * Getter para a cor do status.
     */
    public function getStatusColorAttribute(): string
    {
        return $this->status->color();
    }
}
