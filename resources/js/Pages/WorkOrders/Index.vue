<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link } from '@inertiajs/vue3';
import {
    Table,
    TableBody,
    TableCell,
    TableHead,
    TableHeader,
    TableRow,
} from '@/components/ui/table';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { PlusCircle, MoreHorizontal } from 'lucide-vue-next';
import {
    DropdownMenu,
    DropdownMenuContent,
    DropdownMenuItem,
    DropdownMenuTrigger,
} from '@/components/ui/dropdown-menu';

defineProps({
    workOrders: {
        type: Object,
        required: true,
    },
});

</script>

<template>
    <Head title="Ordens de Serviço" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center justify-between w-full">
                <h2 class="text-xl font-semibold leading-tight">
                    Ordens de Serviço
                </h2>
                <Link :href="route('work-orders.create')">
                    <Button>
                        <PlusCircle class="h-4 w-4 mr-2" />
                        Nova OS
                    </Button>
                </Link>
            </div>
        </template>

        <div class="p-4 sm:p-6 lg:p-8">
            <div class="bg-background border shadow-sm rounded-lg">
                <Table>
                    <TableHeader>
                        <TableRow>
                            <TableHead class="w-[100px]">OS Nº</TableHead>
                            <TableHead>Cliente</TableHead>
                            <TableHead class="hidden md:table-cell">Veículo</TableHead>
                            <TableHead class="hidden lg:table-cell">Total</TableHead>
                            <TableHead>Status</TableHead>
                            <TableHead class="w-[60px]"></TableHead>
                        </TableRow>
                    </TableHeader>
                    <TableBody>
                        <TableRow v-if="workOrders.data.length === 0">
                            <TableCell :colspan="6" class="text-center py-16 text-muted-foreground">
                                <p>Nenhuma Ordem de Serviço encontrada.</p>
                                <Link :href="route('work-orders.create')" class="mt-2 inline-block">
                                    <Button variant="secondary">
                                        <PlusCircle class="h-4 w-4 mr-2" />
                                        Criar Primeira OS
                                    </Button>
                                </Link>
                            </TableCell>
                        </TableRow>
                        <TableRow v-for="wo in workOrders.data" :key="wo.uuid">
                            <TableCell class="font-semibold">#{{ wo.id }}</TableCell>
                            <TableCell>{{ wo.client.name }}</TableCell>
                            <TableCell class="hidden md:table-cell">
                                {{ wo.vehicle.make }} {{ wo.vehicle.model }} ({{ wo.vehicle.plate }})
                            </TableCell>
                            <TableCell class="hidden lg:table-cell">
                                R$ {{ parseFloat(wo.total_price).toFixed(2).replace('.', ',') }}
                            </TableCell>
                            <TableCell>
                                <Badge :variant="wo.status.color">
                                    {{ wo.status.label }}
                                </Badge>
                            </TableCell>
                            <TableCell class="text-right">
                                <DropdownMenu>
                                    <DropdownMenuTrigger as-child>
                                        <Button variant="ghost" class="h-8 w-8 p-0">
                                            <MoreHorizontal class="h-4 w-4" />
                                        </Button>
                                    </DropdownMenuTrigger>
                                    <DropdownMenuContent align="end">
                                        <DropdownMenuItem as-child>
                                            <Link :href="route('work-orders.edit', wo.uuid)">Detalhes</Link>
                                        </DropdownMenuItem>
                                    </DropdownMenuContent>
                                </DropdownMenu>
                            </TableCell>
                        </TableRow>
                    </TableBody>
                </Table>
            </div>
            <div v-if="workOrders.last_page > 1" class="flex justify-center mt-6">
                <div class="flex flex-wrap -mb-1">
                    <template v-for="(link, key) in workOrders.links" :key="key">
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
