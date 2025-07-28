<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, router, useForm } from '@inertiajs/vue3';
import Swal from 'sweetalert2';
import { Card, CardHeader, CardTitle, CardContent } from '@/components/ui/card';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import {
    Select,
    SelectContent,
    SelectItem,
    SelectTrigger,
    SelectValue,
} from '@/components/ui/select';
import { ArrowLeft, Car, User, Wrench, PlusCircle, Package, Trash2 } from 'lucide-vue-next';
import {
    Table,
    TableBody,
    TableCell,
    TableHead,
    TableHeader,
    TableRow,
} from '@/components/ui/table';
import {
    Dialog,
    DialogContent,
    DialogDescription,
    DialogFooter,
    DialogHeader,
    DialogTitle,
    DialogTrigger,
} from '@/components/ui/dialog';
import {
    Command,
    CommandEmpty,
    CommandGroup,
    CommandInput,
    CommandItem,
    CommandList,
} from '@/components/ui/command';
import { computed, ref, watch } from 'vue';
import axios from 'axios';

const props = defineProps({
    workOrder: {
        type: Object,
        required: true,
    },
    mechanics: {
        type: Array,
        required: true,
    }
});

// --- Lógica para Adicionar Serviço ---
const isAddServiceDialogOpen = ref(false);
const serviceSearch = ref('');
const servicesCatalog = ref([]);
let serviceDebounceTimer = null;

const searchServices = async (query) => {
    try {
        const response = await axios.get(route('services.search', { q: query }));
        servicesCatalog.value = response.data;
    } catch (error) { console.error('Erro ao buscar serviços:', error); }
};

watch(serviceSearch, (newValue) => {
    clearTimeout(serviceDebounceTimer);
    serviceDebounceTimer = setTimeout(() => { searchServices(newValue); }, 300);
});

const addServiceToWorkOrder = (serviceId) => {
    router.post(route('work-orders.services.add', props.workOrder.uuid), {
        service_id: serviceId,
    }, {
        preserveScroll: true,
        onSuccess: () => {
            isAddServiceDialogOpen.value = false;
            serviceSearch.value = '';
        }
    });
};

// --- Lógica para Adicionar Peça ---
const isAddPartDialogOpen = ref(false);
const partSearch = ref('');
const partsCatalog = ref([]);
const selectedPart = ref(null);
const partQuantity = ref(1);
let partDebounceTimer = null;

const searchParts = async (query) => {
    try {
        const response = await axios.get(route('parts.search', { q: query }));
        partsCatalog.value = response.data;
    } catch (error) { console.error('Erro ao buscar peças:', error); }
};

watch(partSearch, (newValue) => {
    clearTimeout(partDebounceTimer);
    partDebounceTimer = setTimeout(() => { searchParts(newValue); }, 300);
});

const selectPart = (part) => {
    selectedPart.value = part;
    partSearch.value = part.name;
};

const addPartToWorkOrder = () => {
    if (!selectedPart.value) {
        alert('Por favor, selecione uma peça.');
        return;
    }
    router.post(route('work-orders.parts.add', props.workOrder.uuid), {
        part_id: selectedPart.value.id,
        quantity: partQuantity.value,
    }, {
        preserveScroll: true,
        onSuccess: () => {
            isAddPartDialogOpen.value = false;
            partSearch.value = '';
            selectedPart.value = null;
            partQuantity.value = 1;
        }
    });
};

const confirmRemoveService = (serviceUuid) => {
    Swal.fire({
        title: 'Remover Serviço',
        text: "Tem certeza que deseja remover este serviço da OS?",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Sim, remover!',
        cancelButtonText: 'Cancelar'
    }).then((result) => {
        if (result.isConfirmed) {
            router.delete(route('work-orders.services.remove', [props.workOrder.uuid, serviceUuid]), {
                preserveScroll: true,
            });
        }
    });
};

const confirmRemovePart = (partUuid) => {
    Swal.fire({
        title: 'Remover Peça',
        text: "Tem certeza que deseja remover esta peça da OS?",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Sim, remover!',
        cancelButtonText: 'Cancelar'
    }).then((result) => {
        if (result.isConfirmed) {
            router.delete(route('work-orders.parts.remove', [props.workOrder.uuid, partUuid]), {
                preserveScroll: true,
            });
        }
    });
};

const updateStatus = (newStatus) => {
    Swal.fire({
        title: `Alterar status para "${newStatus.replace('_', ' ').toUpperCase()}"?`,
        text: "Você confirma esta ação?",
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: 'Sim, alterar!',
        cancelButtonText: 'Cancelar'
    }).then((result) => {
        if (result.isConfirmed) {
            router.patch(route('work-orders.status.update', props.workOrder.uuid), {
                status: newStatus,
            }, {
                preserveScroll: true,
            });
        }
    });
};

// Helper para formatar moeda
const formatCurrency = (value) => {
    return parseFloat(value).toLocaleString('pt-BR', { style: 'currency', currency: 'BRL' });
};
const copyClientLink = () => {
    const url = props.workOrder.signed_url;

    navigator.clipboard.writeText(url);

    Swal.fire({
        title: 'Link Copiado!',
        icon: 'success',
        toast: true,
        position: 'top-end',
        showConfirmButton: false,
        timer: 2000,
        timerProgressBar: true,
    });
};

const mechanicForm = useForm({
    mechanic_id: props.workOrder.mechanic_id ? String(props.workOrder.mechanic_id) : null,
});

watch(() => mechanicForm.mechanic_id, (newMechanicId) => {
    router.patch(route('work-orders.assign-mechanic', props.workOrder.uuid), {
        mechanic_id: newMechanicId,
    }, {
        preserveScroll: true,
    });
});
</script>

<template>
    <Head :title="`OS #${workOrder.id}`" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center justify-between w-full">
                <div class="flex items-center gap-4">
                    <Link :href="route('work-orders.index')">
                        <Button variant="outline" size="icon" class="h-8 w-8">
                            <ArrowLeft class="h-4 w-4" />
                        </Button>
                    </Link>
                    <div>
                        <h2 class="text-xl font-semibold leading-tight">
                            Ordem de Serviço #{{ workOrder.id }}
                        </h2>
                        <div class="flex items-center gap-2 mt-1">
                            <p class="text-sm text-muted-foreground">Status:</p>
                            <Badge :variant="workOrder.status_color" class="text-base">
                                {{ workOrder.status_label }}
                            </Badge>
                        </div>
                    </div>
                </div>

                <div v-if="$page.props.auth.user.permissions.includes('edit_work_orders')" class="flex items-center gap-4">
                    <Button @click="copyClientLink" variant="outline" size="sm">
                        Copiar Link do Cliente
                    </Button>
                    <div v-if="['budget', 'approved', 'in_progress', 'awaiting_approval'].includes(workOrder.status)">
                        <Button @click="updateStatus('canceled')" variant="destructive" size="sm">Cancelar OS</Button>
                    </div>

                    <div v-if="workOrder.status === 'budget'">
                        <Button @click="updateStatus('awaiting_approval')" variant="success">Enviar para aprovação</Button>
                    </div>
                    <div v-if="workOrder.status === 'awaiting_approval'">
                        <Button @click="updateStatus('approved')" variant="success">Aprovar orçamento</Button>
                    </div>
                    <div v-else-if="workOrder.status === 'approved'">
                        <Button @click="updateStatus('in_progress')" variant="success">Iniciar Serviço</Button>
                    </div>
                    <div v-else-if="workOrder.status === 'in_progress'">
                        <Button @click="updateStatus('finished')" variant="success">Finalizar Serviço</Button>
                    </div>
                </div>
            </div>
        </template>

        <div class="p-4 sm:p-6 lg:p-8 grid grid-cols-1 lg:grid-cols-3 gap-8 items-start">
            <div class="lg:col-span-2 space-y-8">
                <Card>
                    <CardHeader class="flex flex-row items-center justify-between">
                        <div class="space-y-1.5">
                            <CardTitle class="flex items-center gap-2">
                                <Wrench class="h-5 w-5" />
                                <span>Serviços (Mão de Obra)</span>
                            </CardTitle>
                        </div>
                        <Dialog v-if="$page.props.auth.user.permissions.includes('edit_work_orders') && workOrder.status === 'budget'" v-model:open="isAddServiceDialogOpen">
                            <DialogTrigger as-child>
                                <Button size="sm" variant="outline">
                                    <PlusCircle class="h-4 w-4 mr-2"/>
                                    Adicionar Serviço
                                </Button>
                            </DialogTrigger>
                            <DialogContent>
                                <DialogHeader>
                                    <DialogTitle>Adicionar Serviço à OS</DialogTitle>
                                    <DialogDescription>
                                        Busque no catálogo e selecione um serviço para adicionar.
                                    </DialogDescription>
                                </DialogHeader>
                                <Command :filter-function="(list, query) => list">
                                    <CommandInput v-model="serviceSearch" placeholder="Digite para buscar um serviço..."/>
                                    <CommandList>
                                        <CommandEmpty>Nenhum serviço encontrado.</CommandEmpty>
                                        <CommandGroup>
                                            <CommandItem v-for="service in servicesCatalog" :key="service.id" :value="service.name" @select="() => addServiceToWorkOrder(service.id)" class="cursor-pointer">
                                                <div class="w-full flex justify-between">
                                                    <span>{{ service.name }}</span>
                                                    <span>{{ formatCurrency(service.price) }}</span>
                                                </div>
                                            </CommandItem>
                                        </CommandGroup>
                                    </CommandList>
                                </Command>
                            </DialogContent>
                        </Dialog>
                    </CardHeader>
                    <CardContent>
                        <Table>
                            <TableHeader>
                                <TableRow>
                                    <TableHead>Serviço</TableHead>
                                    <TableHead class="w-[120px] text-right">Preço</TableHead>
                                    <TableHead class="w-[50px]"></TableHead>
                                </TableRow>
                            </TableHeader>
                            <TableBody>
                                <TableRow v-if="workOrder.services.length === 0">
                                    <TableCell :colspan="2" class="text-center py-4 text-muted-foreground">
                                        Nenhum serviço adicionado.
                                    </TableCell>
                                </TableRow>
                                <TableRow v-for="service in workOrder.services" :key="service.id">
                                    <TableCell>{{ service.name }}</TableCell>
                                    <TableCell class="text-right">{{ formatCurrency(service.pivot.price) }}</TableCell>
                                    <TableCell>
                                        <Button v-if="$page.props.auth.user.permissions.includes('edit_work_orders') && workOrder.status === 'budget'" @click="confirmRemoveService(service.uuid)" variant="ghost" size="icon" class="h-8 w-8 text-muted-foreground hover:text-destructive">
                                            <Trash2 class="h-4 w-4" />
                                        </Button>
                                    </TableCell>
                                </TableRow>
                            </TableBody>
                        </Table>
                    </CardContent>
                </Card>

                <Card>
                    <CardHeader class="flex flex-row items-center justify-between">
                        <div class="space-y-1.5">
                            <CardTitle class="flex items-center gap-2">
                                <Package class="h-5 w-5" />
                                <span>Peças</span>
                            </CardTitle>
                        </div>
                        <Dialog v-if="$page.props.auth.user.permissions.includes('edit_work_orders') && workOrder.status === 'budget'" v-model:open="isAddPartDialogOpen">
                            <DialogTrigger as-child>
                                <Button size="sm" variant="outline">
                                    <PlusCircle class="h-4 w-4 mr-2"/>
                                    Adicionar Peça
                                </Button>
                            </DialogTrigger>
                            <DialogContent>
                                <DialogHeader>
                                    <DialogTitle>Adicionar Peça à OS</DialogTitle>
                                    <DialogDescription>
                                        Busque no catálogo, defina a quantidade e adicione a peça.
                                    </DialogDescription>
                                </DialogHeader>

                                <Command :filter-function="(list, query) => list" class="border rounded-md">
                                    <CommandInput v-model="partSearch" placeholder="Digite para buscar uma peça..."/>
                                    <CommandList>
                                        <CommandEmpty>Nenhuma peça encontrada.</CommandEmpty>
                                        <CommandGroup>
                                            <CommandItem v-for="part in partsCatalog" :key="part.id" :value="part.name" @select="() => selectPart(part)" class="cursor-pointer">
                                                <div>
                                                    <p>{{ part.name }}</p>
                                                    <p class="text-xs text-muted-foreground">Estoque: {{ part.quantity }} | R$ {{ part.sale_price }}</p>
                                                </div>
                                            </CommandItem>
                                        </CommandGroup>
                                    </CommandList>
                                </Command>

                                <div class="flex justify-between items-center pt-4">
                                    <Label for="quantity" class="text-base">Quantidade</Label>
                                    <Input id="quantity" type="number" v-model="partQuantity" class="w-28 text-center text-base font-semibold" min="1"/>
                                </div>

                                <DialogFooter>
                                    <Button @click="addPartToWorkOrder" :disabled="!selectedPart">Adicionar à OS</Button>
                                </DialogFooter>
                            </DialogContent>
                        </Dialog>
                    </CardHeader>
                    <CardContent>
                        <Table>
                            <TableHeader>
                                <TableRow>
                                    <TableHead>Peça</TableHead>
                                    <TableHead>Qtd.</TableHead>
                                    <TableHead>Preço Unit.</TableHead>
                                    <TableHead class="text-right">Subtotal</TableHead>
                                    <TableHead class="w-[50px]"></TableHead>
                                </TableRow>
                            </TableHeader>
                            <TableBody>
                                <TableRow v-if="workOrder.parts.length === 0">
                                    <TableCell :colspan="4" class="text-center py-4 text-muted-foreground">
                                        Nenhuma peça adicionada.
                                    </TableCell>
                                </TableRow>
                                <TableRow v-for="part in workOrder.parts" :key="part.id">
                                    <TableCell>{{ part.name }}</TableCell>
                                    <TableCell>{{ part.pivot.quantity }}</TableCell>
                                    <TableCell>{{ formatCurrency(part.pivot.sale_price) }}</TableCell>
                                    <TableCell class="text-right">{{ formatCurrency(part.pivot.quantity * part.pivot.sale_price) }}</TableCell>
                                    <TableCell>
                                        <Button v-if="$page.props.auth.user.permissions.includes('edit_work_orders') && workOrder.status === 'budget'" @click="confirmRemovePart(part.uuid)" variant="ghost" size="icon" class="h-8 w-8 text-muted-foreground hover:text-destructive">
                                            <Trash2 class="h-4 w-4" />
                                        </Button>
                                    </TableCell>
                                </TableRow>
                            </TableBody>
                        </Table>
                    </CardContent>
                </Card>
            </div>

            <div class="lg:col-span-1 space-y-8">
                <Card>
                    <CardHeader>
                        <CardTitle class="flex items-center gap-2">
                            <span>Responsável Técnico</span>
                        </CardTitle>
                    </CardHeader>
                    <CardContent class="space-y-4">
                        <div>
                            <Label for="mechanic_id">Mecânico</Label>
                            <Select v-model="mechanicForm.mechanic_id">
                                <SelectTrigger>
                                    <SelectValue placeholder="Nenhum mecânico atribuído" />
                                </SelectTrigger>
                                <SelectContent>
                                    <SelectItem v-for="mechanic in mechanics" :key="mechanic.id" :value="String(mechanic.id)">
                                        {{ mechanic.name }}
                                    </SelectItem>
                                </SelectContent>
                            </Select>
                        </div>
                    </CardContent>
                </Card>
                <Card>
                    <CardHeader>
                        <CardTitle class="flex items-center gap-2">
                            <User class="h-5 w-5" />
                            <span>Cliente</span>
                        </CardTitle>
                    </CardHeader>
                    <CardContent class="text-sm space-y-2">
                        <p class="font-semibold">{{ workOrder.client.name }}</p>
                        <p class="text-muted-foreground">{{ workOrder.client.document_number }}</p>
                        <p class="text-muted-foreground">{{ workOrder.client.email }}</p>
                        <p class="text-muted-foreground">{{ workOrder.client.phone_number }}</p>
                    </CardContent>
                </Card>
                <Card>
                    <CardHeader>
                        <CardTitle class="flex items-center gap-2">
                            <Car class="h-5 w-5" />
                            <span>Veículo</span>
                        </CardTitle>
                    </CardHeader>
                    <CardContent class="text-sm space-y-2">
                        <p class="font-semibold">{{ workOrder.vehicle.make }} {{ workOrder.vehicle.model }}</p>
                        <p class="text-muted-foreground">Placa: {{ workOrder.vehicle.plate }}</p>
                        <p class="text-muted-foreground">Ano: {{ workOrder.vehicle.year }}</p>
                        <p class="text-muted-foreground">Cor: {{ workOrder.vehicle.color }}</p>
                    </CardContent>
                </Card>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
