<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, router  } from '@inertiajs/vue3';
import {
    Table,
    TableBody,
    TableCell,
    TableHead,
    TableHeader,
    TableRow,
} from '@/components/ui/table';
import {
    DropdownMenu,
    DropdownMenuContent,
    DropdownMenuItem,
    DropdownMenuTrigger,
} from '@/components/ui/dropdown-menu';
import { Button } from '@/components/ui/button';
import { PlusCircle, MoreHorizontal } from 'lucide-vue-next';
import Swal from 'sweetalert2';

const confirmDelete = (vehicleUuid) => {
    Swal.fire({
        title: 'Você tem certeza?',
        text: "Esta ação não poderá ser revertida!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Sim, excluir!',
        cancelButtonText: 'Cancelar'
    }).then((result) => {
        if (result.isConfirmed) {
            router.delete(route('vehicles.destroy', vehicleUuid));
        }
    });
};

defineProps({
    vehicles: {
        type: Object,
        required: true,
    },
});

</script>

<template>
    <Head title="Veículos" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center justify-between w-full">
                <h2 class="text-xl font-semibold leading-tight">
                    Gestão de Veículos
                </h2>
                <Link v-if="$page.props.auth.user.permissions.includes('create_vehicles')" :href="route('vehicles.create')">
                    <Button>
                        <PlusCircle class="h-4 w-4 mr-2" />
                        Novo Veículo
                    </Button>
                </Link>
            </div>
        </template>

        <div class="p-4 sm:p-6 lg:p-8">
            <div class="bg-background border shadow-sm rounded-lg">
                <Table>
                    <TableHeader>
                        <TableRow>
                            <TableHead>Placa</TableHead>
                            <TableHead>Veículo</TableHead>
                            <TableHead class="hidden md:table-cell">Proprietário</TableHead>
                            <TableHead class="hidden lg:table-cell">Ano</TableHead>
                            <TableHead class="w-[60px]"></TableHead>
                        </TableRow>
                    </TableHeader>
                    <TableBody>
                        <TableRow v-if="vehicles.data.length === 0">
                            <TableCell :colspan="5" class="text-center py-16 text-muted-foreground">
                                <p>Nenhum veículo encontrado.</p>
                                <Link :href="route('vehicles.create')" class="mt-2 inline-block">
                                    <Button variant="secondary">
                                        <PlusCircle class="h-4 w-4 mr-2" />
                                        Cadastrar Primeiro Veículo
                                    </Button>
                                </Link>
                            </TableCell>
                        </TableRow>
                        <TableRow v-for="vehicle in vehicles.data" :key="vehicle.uuid">
                            <TableCell class="font-mono font-semibold">{{ vehicle.plate }}</TableCell>
                            <TableCell>
                                <div class="font-medium">{{ vehicle.make }} {{ vehicle.model }}</div>
                                <div class="text-sm text-muted-foreground">{{ vehicle.color }}</div>
                            </TableCell>
                            <TableCell class="hidden md:table-cell">{{ vehicle.client.name }}</TableCell>
                            <TableCell class="hidden lg:table-cell">{{ vehicle.year }}</TableCell>
                            <TableCell class="text-right">
                                <DropdownMenu>
                                    <DropdownMenuTrigger as-child>
                                        <Button variant="ghost" class="h-8 w-8 p-0">
                                            <span class="sr-only">Abrir menu</span>
                                            <MoreHorizontal class="h-4 w-4" />
                                        </Button>
                                    </DropdownMenuTrigger>
                                    <DropdownMenuContent align="end">
                                        <DropdownMenuItem v-if="$page.props.auth.user.permissions.includes('edit_vehicles')" as-child>
                                            <Link :href="route('vehicles.edit', vehicle.uuid)">Editar</Link>
                                        </DropdownMenuItem>
                                        <DropdownMenuItem v-if="$page.props.auth.user.permissions.includes('delete_vehicles')" @click="confirmDelete(vehicle.uuid)" class="text-red-600 focus:text-red-600 cursor-pointer">
                                            Excluir
                                        </DropdownMenuItem>
                                    </DropdownMenuContent>
                                </DropdownMenu>
                            </TableCell>
                        </TableRow>
                    </TableBody>
                </Table>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
