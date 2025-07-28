<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Part;

class PartSeeder extends Seeder
{
    public function run(): void
    {
        Part::create([
            'name' => 'Pastilha de Freio Dianteira',
            'sku' => 'PD-1023',
            'brand' => 'Bosch',
            'cost_price' => 80.50,
            'sale_price' => 150.00,
            'quantity' => 20,
        ]);

        Part::create([
            'name' => 'Óleo de Motor 5W-30 Sintético (Litro)',
            'sku' => 'OL-5W30',
            'brand' => 'Mobil',
            'cost_price' => 35.00,
            'sale_price' => 65.00,
            'quantity' => 50,
        ]);

        Part::create([
            'name' => 'Filtro de Ar do Motor',
            'sku' => 'FA-405',
            'brand' => 'Fram',
            'cost_price' => 25.00,
            'sale_price' => 45.00,
            'quantity' => 30,
        ]);
    }
}
