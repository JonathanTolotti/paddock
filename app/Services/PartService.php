<?php

namespace App\Services;

use App\Models\Part;

class PartService
{
    public function create(array $data): Part
    {
        return Part::create($data);
    }

    public function update(Part $part, array $data): Part
    {
        $part->update($data);
        return $part;
    }

    public function delete(Part $part): void
    {
        $part->delete();
    }
}
