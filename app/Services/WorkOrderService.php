<?php

namespace App\Services;

use App\Models\WorkOrder;

class WorkOrderService
{
    public function create(array $data): WorkOrder
    {
        return WorkOrder::create($data);
    }

}
