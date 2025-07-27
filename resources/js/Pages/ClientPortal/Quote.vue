<script setup>
import PublicLayout from '@/Layouts/PublicLayout.vue';
import { Card, CardHeader, CardTitle, CardDescription, CardContent } from '@/components/ui/card';
import {
    Table,
    TableBody,
    TableCell,
    TableHead,
    TableHeader,
    TableRow,
} from '@/components/ui/table';
import { computed } from 'vue';
import { format } from 'date-fns';
import { ptBR } from 'date-fns/locale';
import { FileScan, CheckCircle2, Wrench, Flag, XCircle, Hourglass } from 'lucide-vue-next';
import { router } from '@inertiajs/vue3';
import Swal from 'sweetalert2';
import {Button} from '@/components/ui/button';

const props = defineProps({
    workOrder: {
        type: Object,
        required: true,
    },
    statusHistories: {
        type: Array,
        required: true,
    },
    approveUrl: {
        type: String,
        required: true,
    },
    rejectUrl: {
        type: String,
        required: true,
    },
});

// Helper para formatar moeda
const formatCurrency = (value) => {
    return parseFloat(value).toLocaleString('pt-BR', { style: 'currency', currency: 'BRL' });
};

// Helper para formatar data e hora
const formatDateTime = (value) => {
    if (!value) return '';
    return format(new Date(value), "dd/MM/yyyy 'às' HH:mm", { locale: ptBR });
};

// Mapa para associar o valor do status a um ícone e a uma cor
const statusMeta = {
    budget: { icon: FileScan, color: 'text-slate-500' },
    awaiting_approval: { icon: Hourglass, color: 'text-amber-500' },
    approved: { icon: CheckCircle2, color: 'text-blue-500' },
    in_progress: { icon: Wrench, color: 'text-amber-500' },
    finished: { icon: Flag, color: 'text-green-500' },
    canceled: { icon: XCircle, color: 'text-red-500' },
};
const approveQuote = () => {
    router.post(props.approveUrl);
};

const rejectQuote = () => {
    Swal.fire({
        title: 'Reprovar Orçamento',
        text: "Você tem certeza que deseja reprovar este orçamento?",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Sim, reprovar',
        cancelButtonText: 'Cancelar'
    }).then((result) => {
        if (result.isConfirmed) {
            router.post(props.rejectUrl);
        }
    });
};


</script>

<template>
    <PublicLayout>
        <div class="container mx-auto max-w-7xl grid grid-cols-1 lg:grid-cols-4 gap-8 items-start">
            <div class="lg:col-span-3">
                <Card class="overflow-hidden">
                    <CardHeader class="p-6 bg-muted/40 flex flex-col md:flex-row items-start md:items-center justify-between gap-4">
                        <div>
                            <CardTitle class="text-2xl">Orçamento #{{ workOrder.id }}</CardTitle>
                            <CardDescription>
                                Preparado para: {{ workOrder.client.name }}
                            </CardDescription>
                        </div>
                        <div v-if="workOrder.status" class="inline-flex items-center rounded-full border px-3 py-1 text-sm font-semibold transition-colors"
                             :class="{
                                'bg-slate-500 text-secondary-foreground': workOrder.status.value === 'budget',
                                'bg-blue-600 text-primary-foreground': workOrder.status.value === 'approved',
                                'text-foreground border-border': workOrder.status.value === 'in_progress',
                                'bg-green-600 text-primary-foreground': workOrder.status.value === 'finished',
                                'bg-destructive text-destructive-foreground': workOrder.status.value === 'canceled',
                             }">
                            {{ workOrder.status.label }}
                        </div>
                    </CardHeader>
                    <CardContent class="p-6 space-y-8">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 border-b pb-6">
                            <div>
                                <h3 class="font-semibold mb-2">Cliente</h3>
                                <div class="text-sm text-muted-foreground space-y-1">
                                    <p><span class="font-medium text-foreground">Nome:</span> {{ workOrder.client.name }}</p>
                                    <p><span class="font-medium text-foreground">Telefone:</span> {{ workOrder.client.phone_number }}</p>
                                </div>
                            </div>
                            <div>
                                <h3 class="font-semibold mb-2">Veículo</h3>
                                <div class="text-sm text-muted-foreground space-y-1">
                                    <p><span class="font-medium text-foreground">Modelo:</span> {{ workOrder.vehicle.make }} {{ workOrder.vehicle.model }}</p>
                                    <p><span class="font-medium text-foreground">Placa:</span> {{ workOrder.vehicle.plate }}</p>
                                </div>
                            </div>
                        </div>

                        <div class="space-y-4">
                            <div>
                                <h3 class="font-semibold mb-2">Problema Relatado</h3>
                                <p class="text-sm text-muted-foreground whitespace-pre-wrap">{{ workOrder.problem_reported || 'Nenhum problema relatado.' }}</p>
                            </div>
                            <div>
                                <h3 class="font-semibold mb-2">Diagnóstico Técnico</h3>
                                <p class="text-sm text-muted-foreground whitespace-pre-wrap">{{ workOrder.technical_diagnosis || 'Aguardando diagnóstico.' }}</p>
                            </div>
                        </div>

                        <div v-if="workOrder.services.length > 0">
                            <h3 class="font-semibold mb-4">Serviços Propostos</h3>
                            <div class="border rounded-lg">
                                <Table>
                                    <TableHeader>
                                        <TableRow>
                                            <TableHead>Serviço</TableHead>
                                            <TableHead class="text-right">Preço</TableHead>
                                        </TableRow>
                                    </TableHeader>
                                    <TableBody>
                                        <TableRow v-for="service in workOrder.services" :key="service.id">
                                            <TableCell class="font-medium">{{ service.name }}</TableCell>
                                            <TableCell class="text-right">{{ formatCurrency(service.pivot.price) }}</TableCell>
                                        </TableRow>
                                    </TableBody>
                                </Table>
                            </div>
                        </div>

                        <div v-if="workOrder.parts.length > 0">
                            <h3 class="font-semibold mb-4">Peças a Utilizar</h3>
                            <div class="border rounded-lg">
                                <Table>
                                    <TableHeader>
                                        <TableRow>
                                            <TableHead>Peça</TableHead>
                                            <TableHead>Qtd.</TableHead>
                                            <TableHead>Preço Unit.</TableHead>
                                            <TableHead class="text-right">Subtotal</TableHead>
                                        </TableRow>
                                    </TableHeader>
                                    <TableBody>
                                        <TableRow v-for="part in workOrder.parts" :key="part.id">
                                            <TableCell class="font-medium">{{ part.name }}</TableCell>
                                            <TableCell>{{ part.pivot.quantity }}</TableCell>
                                            <TableCell>{{ formatCurrency(part.pivot.sale_price) }}</TableCell>
                                            <TableCell class="text-right">{{ formatCurrency(part.pivot.quantity * part.pivot.sale_price) }}</TableCell>
                                        </TableRow>
                                    </TableBody>
                                </Table>
                            </div>
                        </div>

                        <div class="flex justify-end pt-6 border-t">
                            <div class="w-full max-w-sm space-y-2">
                                <div class="flex justify-between">
                                    <span class="text-muted-foreground">Subtotal (Peças)</span>
                                    <span>{{ formatCurrency(workOrder.total_parts_price) }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-muted-foreground">Subtotal (Serviços)</span>
                                    <span>{{ formatCurrency(workOrder.total_services_price) }}</span>
                                </div>
                                <div class="flex justify-between font-bold text-lg border-t pt-2 mt-2">
                                    <span>TOTAL DO ORÇAMENTO</span>
                                    <span>{{ formatCurrency(workOrder.total_price) }}</span>
                                </div>
                            </div>
                        </div>

                        <div class="border-t pt-6 mt-6">
                            <div v-if="workOrder.status === 'awaiting_approval'" class="flex flex-col md:flex-row items-center justify-center gap-4">
                                <Button @click="rejectQuote" variant="destructive" class="w-full md:w-auto">
                                    Reprovar Orçamento
                                </Button>
                                <Button @click="approveQuote" variant="success" class="w-full md:w-auto">
                                    Aprovar Orçamento
                                </Button>
                            </div>
                            <div v-else class="text-center">
                                <p class="text-muted-foreground">Este orçamento já foi processado ou ainda não foi liberado.</p>
                            </div>
                        </div>
                    </CardContent>
                </Card>
            </div>

            <div class="lg:col-span-1">
                <Card v-if="statusHistories && statusHistories.length > 0">
                    <CardHeader>
                        <CardTitle>Histórico</CardTitle>
                    </CardHeader>
                    <CardContent class="p-6">
                        <div class="relative">
                            <div class="absolute left-3 top-3 h-full w-0.5 bg-border -z-10"></div>
                            <ul class="space-y-8">
                                <li v-for="history in statusHistories" :key="history.id" class="flex items-start">
                                    <div class="flex h-6 w-6 items-center justify-center rounded-full bg-background border mr-4" :class="statusMeta[history.status]?.color">
                                        <component :is="statusMeta[history.status]?.icon" class="h-4 w-4" />
                                    </div>
                                    <div class="flex-1">
                                        <p class="font-semibold">{{ history.status_label }}</p>
                                        <time class="text-xs text-muted-foreground">{{ formatDateTime(history.created_at) }}</time>
                                    </div>
                                </li>
                            </ul>
                        </div>
                    </CardContent>
                </Card>
            </div>
        </div>
    </PublicLayout>
</template>
