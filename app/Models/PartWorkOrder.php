<?php

namespace App\Models;

use App\Observers\PartWorkOrderObserver;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Relations\Pivot;
#[ObservedBy(PartWorkOrderObserver::class)]
class PartWorkOrder extends Pivot
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'part_work_order';
}
