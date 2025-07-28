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

defineProps({
    parts: {
        type: Object,
        required: true,
    },
});

const confirmDelete = (partUuid) => {
    Swal.fire({
        title: 'Você tem certeza?',
        text: "A peça será movida para a lixeira!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Sim, excluir!',
        cancelButtonText: 'Cancelar'
    }).then((result) => {
        if (result.isConfirmed) {
            router.delete(route('parts.destroy', partUuid));
        }
    });
};

const formatCurrency = (value) => {
    return parseFloat(value).toLocaleString('pt-BR', { style: 'currency', currency: 'BRL' });
};
</script>

<template>
    <Head title="Estoque de Peças" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center justify-between w-full">
                <h2 class="text-xl font-semibold leading-tight">
                    Estoque de Peças
                </h2>
                <Link v-if="$page.props.auth.user.permissions.includes('manage_catalog')" :href="route('parts.create')">
                    <Button>
                        <PlusCircle class="h-4 w-4 mr-2" />
                        Nova Peça
                    </Button>
                </Link>
            </div>
        </template>

        <div class="p-4 sm:p-6 lg:p-8">
            <div class="bg-background border shadow-sm rounded-lg">
                <Table>
                    <TableHeader>
                        <TableRow>
                            <TableHead>Nome da Peça</TableHead>
                            <TableHead class="hidden md:table-cell">Marca</TableHead>
                            <TableHead class="hidden lg:table-cell">SKU</TableHead>
                            <TableHead>Preço de Venda</TableHead>
                            <TableHead>Estoque</TableHead>
                            <TableHead class="w-[60px]"></TableHead>
                        </TableRow>
                    </TableHeader>
                    <TableBody>
                        <TableRow v-if="parts.data.length === 0">
                            <TableCell :colspan="6" class="text-center py-16 text-muted-foreground">
                                <p>Nenhuma peça encontrada.</p>
                                <Link :href="route('parts.create')" class="mt-2 inline-block">
                                    <Button variant="secondary">
                                        <PlusCircle class="h-4 w-4 mr-2" />
                                        Cadastrar Primeira Peça
                                    </Button>
                                </Link>
                            </TableCell>
                        </TableRow>
                        <TableRow v-for="part in parts.data" :key="part.uuid">
                            <TableCell class="font-medium">{{ part.name }}</TableCell>
                            <TableCell class="hidden md:table-cell">{{ part.brand }}</TableCell>
                            <TableCell class="hidden lg:table-cell font-mono">{{ part.sku }}</TableCell>
                            <TableCell>{{ formatCurrency(part.sale_price) }}</TableCell>
                            <TableCell>
                                <div class="font-semibold">{{ part.quantity }}</div>
                                <div v-if="part.quantity_reserved > 0" class="text-xs text-amber-600">
                                    ({{ part.quantity_reserved }} reservado)
                                </div>
                            </TableCell>
                            <TableCell class="text-right">
                                <DropdownMenu v-if="$page.props.auth.user.permissions.includes('manage_catalog')">
                                    <DropdownMenuTrigger as-child>
                                        <Button variant="ghost" class="h-8 w-8 p-0">
                                            <MoreHorizontal class="h-4 w-4" />
                                        </Button>
                                    </DropdownMenuTrigger>
                                    <DropdownMenuContent align="end">
                                        <DropdownMenuItem as-child>
                                            <Link :href="route('parts.edit', part.uuid)">Editar</Link>
                                        </DropdownMenuItem>
                                        <DropdownMenuItem @click="confirmDelete(part.uuid)" class="text-red-600 focus:text-red-600 cursor-pointer">
                                            Excluir
                                        </DropdownMenuItem>
                                    </DropdownMenuContent>
                                </DropdownMenu>
                            </TableCell>
                        </TableRow>
                    </TableBody>
                </Table>
            </div>
            <div v-if="parts.last_page > 1" class="flex justify-center mt-6">
                <div class="flex flex-wrap -mb-1">
                    <template v-for="(link, key) in parts.links" :key="key">
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
