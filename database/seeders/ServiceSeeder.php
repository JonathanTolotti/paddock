<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Service;

class ServiceSeeder extends Seeder
{
    public function run(): void
    {
        Service::create([
            'name' => 'Troca de Óleo e Filtro',
            'description' => 'Inclui a troca do óleo do motor e do filtro de óleo.',
            'price' => 120.00,
        ]);

        Service::create([
            'name' => 'Alinhamento e Balanceamento',
            'description' => 'Alinhamento da direção e balanceamento das 4 rodas.',
            'price' => 150.00,
        ]);

        Service::create([
            'name' => 'Diagnóstico com Scanner',
            'description' => 'Leitura de códigos de falha da injeção eletrônica.',
            'price' => 100.00,
        ]);
    }
}
