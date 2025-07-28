<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Client;
use App\Models\Vehicle;

class ClientSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Cliente 1 com dois veículos
        $client1 = Client::create([
            'name' => 'João da Silva',
            'document_type' => 'CPF',
            'document_number' => '11122233344',
            'email' => 'joao.silva@example.com',
            'phone_number' => '51999998888',
        ]);

        Vehicle::create([
            'client_id' => $client1->id,
            'plate' => 'IVX4E51',
            'make' => 'Chevrolet',
            'model' => 'Onix',
            'year' => 2020,
            'color' => 'Prata',
        ]);

        Vehicle::create([
            'client_id' => $client1->id,
            'plate' => 'ABC1234',
            'make' => 'Volkswagen',
            'model' => 'Gol',
            'year' => 2018,
            'color' => 'Branco',
        ]);

        // Cliente 2 com um veículo
        $client2 = Client::create([
            'name' => 'Maria Oliveira',
            'document_type' => 'CPF',
            'document_number' => '22233344455',
            'email' => 'maria.oliveira@example.com',
            'phone_number' => '11988887777',
        ]);

        Vehicle::create([
            'client_id' => $client2->id,
            'plate' => 'DEF5678',
            'make' => 'Fiat',
            'model' => 'Mobi',
            'year' => 2022,
            'color' => 'Vermelho',
        ]);

        // Cliente 3 (Empresa) com um veículo
        $client3 = Client::create([
            'name' => 'Auto Peças Veloz Ltda',
            'document_type' => 'CNPJ',
            'document_number' => '12345678000199',
            'email' => 'contato@velozapecas.com',
            'phone_number' => '2122223333',
        ]);

        Vehicle::create([
            'client_id' => $client3->id,
            'plate' => 'GHI9A01',
            'make' => 'Ford',
            'model' => 'Ranger',
            'year' => 2023,
            'color' => 'Azul',
        ]);
    }
}
