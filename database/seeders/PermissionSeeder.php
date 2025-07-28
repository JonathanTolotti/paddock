<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class PermissionSeeder extends Seeder
{
    public function run(): void
    {
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        // Cria as permissões
        $permissions = [
            // Clientes
            'view_clients',
            'create_clients',
            'edit_clients',
            'delete_clients',
            // Veículos
            'view_vehicles',
            'create_vehicles',
            'edit_vehicles',
            'delete_vehicles',
            // Ordens de Serviço
            'view_work_orders',
            'create_work_orders',
            'edit_work_orders',
            'delete_work_orders',
            // Catálogos (Peças e Serviços)
            'manage_catalog',
            // Usuários
            'manage_users',
        ];

        foreach ($permissions as $permission) {
            Permission::create(['name' => $permission]);
        }

        // ATENDENTE
        $atendenteRole = Role::findByName('Atendente');
        $atendenteRole->givePermissionTo([
            'view_clients',
            'create_clients',
            'edit_clients',
            'view_vehicles',
            'create_vehicles',
            'edit_vehicles',
            'view_work_orders',
            'create_work_orders',
            'edit_work_orders',
        ]);

        // MECÂNICO
        $mecanicoRole = Role::findByName('Mecânico');
        $mecanicoRole->givePermissionTo([
            'view_work_orders',
            'edit_work_orders',
            'view_vehicles',
        ]);

        $adminRole = Role::findByName('Admin');
        $adminRole->givePermissionTo(Permission::all());
    }
}
