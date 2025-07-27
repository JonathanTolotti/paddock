<?php

namespace App\Observers;

use App\Models\Part;
use App\Models\PartWorkOrder;

class PartWorkOrderObserver
{
    /**
     * Handle the PartWorkOrder "created" event.
     */
    public function created(PartWorkOrder $partWorkOrder): void
    {
        $part = Part::find($partWorkOrder->part_id);
        if ($part) {
            $part->quantity_reserved += $partWorkOrder->quantity;
            $part->save();
        }
    }

    /**
     * Handle the PartWorkOrder "updated" event.
     */
    public function updated(PartWorkOrder $partWorkOrder): void
    {
        $originalQuantity = $partWorkOrder->getOriginal('quantity', 0);
        $newQuantity = $partWorkOrder->quantity;

        $quantityDiff = $newQuantity - $originalQuantity;

        $part = Part::find($partWorkOrder->part_id);
        if ($part) {
            $part->quantity_reserved = max(0, $part->quantity_reserved + $quantityDiff);
            $part->save();
        }
    }

    /**
     * Handle the PartWorkOrder "deleted" event.
     */
    public function deleted(PartWorkOrder $partWorkOrder): void
    {
        $part = Part::find($partWorkOrder->part_id);
        if ($part) {
            $part->quantity_reserved = max(0, $part->quantity_reserved - $partWorkOrder->quantity);
            $part->save();
        }
    }

    /**
     * Handle the PartWorkOrder "restored" event.
     */
    public function restored(PartWorkOrder $partWorkOrder): void
    {
        //
    }

    /**
     * Handle the PartWorkOrder "force deleted" event.
     */
    public function forceDeleted(PartWorkOrder $partWorkOrder): void
    {
        //
    }
}
