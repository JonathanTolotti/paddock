<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, router } from '@inertiajs/vue3';
import Swal from 'sweetalert2';
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

const props = defineProps({
    services: {
        type: Object,
        required: true,
    },
});

const formatCurrency = (value) => {
    return parseFloat(value).toLocaleString('pt-BR', { style: 'currency', currency: 'BRL' });
};

const confirmDelete = (serviceUuid) => {
    Swal.fire({
        title: 'Você tem certeza?',
        text: "O serviço será movido para a lixeira!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Sim, excluir!',
        cancelButtonText: 'Cancelar'
    }).then((result) => {
        if (result.isConfirmed) {
            router.delete(route('services.destroy', serviceUuid));
        }
    });
};
</script>

<template>
    <Head title="Serviços" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center justify-between w-full">
                <h2 class="text-xl font-semibold leading-tight">Catálogo de Serviços</h2>
                <Link :href="route('services.create')">
                    <Button>
                        <PlusCircle class="h-4 w-4 mr-2" />
                        Novo Serviço
                    </Button>
                </Link>
            </div>
        </template>

        <div class="p-4 sm:p-6 lg:p-8">
            <div class="bg-background border shadow-sm rounded-lg">
                <Table>
                    <TableHeader>
                        <TableRow>
                            <TableHead>Nome do Serviço</TableHead>
                            <TableHead class="hidden md:table-cell">Descrição</TableHead>
                            <TableHead>Preço</TableHead>
                            <TableHead class="w-[60px]"></TableHead>
                        </TableRow>
                    </TableHeader>
                    <TableBody>
                        <TableRow v-if="services.data.length === 0">
                            <TableCell :colspan="4" class="text-center py-16 text-muted-foreground">
                                <p>Nenhum serviço encontrado.</p>
                                <Link :href="route('services.create')" class="mt-2 inline-block">
                                    <Button variant="secondary">
                                        <PlusCircle class="h-4 w-4 mr-2" />
                                        Cadastrar Primeiro Serviço
                                    </Button>
                                </Link>
                            </TableCell>
                        </TableRow>
                        <TableRow v-for="service in services.data" :key="service.uuid">
                            <TableCell class="font-medium">{{ service.name }}</TableCell>
                            <TableCell class="hidden md:table-cell text-sm text-muted-foreground">{{ service.description }}</TableCell>
                            <TableCell>{{ formatCurrency(service.price) }}</TableCell>
                            <TableCell class="text-right">
                                <DropdownMenu>
                                    <DropdownMenuTrigger as-child>
                                        <Button variant="ghost" class="h-8 w-8 p-0">
                                            <span class="sr-only">Abrir menu</span>
                                            <MoreHorizontal class="h-4 w-4" />
                                        </Button>
                                    </DropdownMenuTrigger>
                                    <DropdownMenuContent align="end">
                                        <DropdownMenuItem as-child>
                                            <Link :href="route('services.edit', service.uuid)">Editar</Link>
                                        </DropdownMenuItem>
                                        <DropdownMenuItem @click="confirmDelete(service.uuid)" class="text-red-600 focus:text-red-600 cursor-pointer">
                                            Excluir
                                        </DropdownMenuItem>
                                    </DropdownMenuContent>
                                </DropdownMenu>
                            </TableCell>
                        </TableRow>
                    </TableBody>
                </Table>
            </div>

            <div v-if="services.last_page > 1" class="flex justify-center mt-6">
                <div class="flex flex-wrap -mb-1">
                    <template v-for="(link, key) in services.links" :key="key">
                        <div v-if="link.url === null"
                             class="mr-1 mb-1 px-3 py-2 text-sm leading-4 text-gray-400 border rounded bg-muted/40"
                             v-html="link.label" />
                        <Link v-else
                              class="mr-1 mb-1 px-3 py-2 text-sm leading-4 border rounded hover:bg-muted focus:border-primary focus:text-primary"
                              :class="{ 'bg-primary text-primary-foreground hover:bg-primary/90': link.active }"
                              :href="link.url"
                              preserve-scroll
                              v-html="link.label" />
                    </template>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
