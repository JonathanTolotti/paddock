<?php

namespace App\Models;

use App\Enums\WorkOrderStatus;
use Illuminate\Database\Eloquent\Model;

class WorkOrderStatusHistory extends Model
{
    protected $fillable = [
        'work_order_id',
        'status',
        'user_id',
        'notes',
    ];

    protected $casts = [
        'status' => WorkOrderStatus::class,
    ];

    protected $appends = [
        'status_label',
    ];

    /**
     * Getter para o label do status.
     */
    public function getStatusLabelAttribute(): string
    {
        return $this->status->label();
    }
}
