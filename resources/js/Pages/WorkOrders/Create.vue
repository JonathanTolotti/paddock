<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import { Button } from '@/components/ui/button';
import { Label } from '@/components/ui/label';
import { Textarea } from '@/components/ui/textarea';
import {
    Select,
    SelectContent,
    SelectItem,
    SelectTrigger,
    SelectValue,
} from '@/components/ui/select';
import { Card, CardHeader, CardTitle, CardDescription, CardContent, CardFooter } from '@/components/ui/card';
import { ArrowLeft, LoaderCircle, Check, ChevronsUpDown } from 'lucide-vue-next';
import { ref, watch } from 'vue';
import axios from 'axios';
import {
    Command,
    CommandEmpty,
    CommandGroup,
    CommandInput,
    CommandItem,
    CommandList,
} from '@/components/ui/command';
import {
    Popover,
    PopoverContent,
    PopoverTrigger,
} from '@/components/ui/popover';

const props = defineProps({
    clients: {
        type: Array,
        required: true,
    },
});

const form = useForm({
    client_id: null,
    vehicle_id: null,
    problem_reported: '',
});

// --- Lógica do Combobox de Clientes ---
const clientsList = ref(props.clients);
const open = ref(false);
const selectedClient = ref(null);
const search = ref('');
let debounceTimer = null;

const searchClients = async (query) => {
    try {
        const response = await axios.get(route('clients.search', { q: query }));
        clientsList.value = response.data;
    } catch (error) {
        console.error('Erro ao buscar clientes:', error);
    }
};

watch(search, (newValue) => {
    clearTimeout(debounceTimer);
    debounceTimer = setTimeout(() => {
        searchClients(newValue);
    }, 300);
});

// --- Lógica do Formulário Dinâmico (Corrigida) ---
const vehicles = ref([]);
const isLoadingVehicles = ref(false);

const fetchVehicles = async (clientUuid) => {
    vehicles.value = [];
    form.vehicle_id = null;

    isLoadingVehicles.value = true;
    try {
        const url = route('clients.vehicles', clientUuid);

        const response = await axios.get(url);
        vehicles.value = response.data;
    } catch (error) {
    } finally {
        isLoadingVehicles.value = false;
    }
};

function onClientSelect(client) {
    console.log(client);
    selectedClient.value = client;
    form.client_id = client.id;
    open.value = false;

    // Dispara a busca de veículos DIRETAMENTE AQUI
    fetchVehicles(client.uuid);
}

const submit = () => {
    form.post(route('work-orders.store'));
};
</script>

<template>
    <Head title="Nova Ordem de Serviço" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center gap-4">
                <Link :href="route('work-orders.index')">
                    <Button variant="outline" size="icon" class="h-8 w-8">
                        <ArrowLeft class="h-4 w-4" />
                    </Button>
                </Link>
                <h2 class="text-xl font-semibold leading-tight">Criar Nova Ordem de Serviço</h2>
            </div>
        </template>

        <div class="p-4 sm:p-6 lg:p-8 max-w-4xl mx-auto">
            <Card>
                <form @submit.prevent="submit">
                    <CardHeader>
                        <CardTitle>Dados da OS</CardTitle>
                        <CardDescription>Selecione o cliente e o veículo para iniciar a OS.</CardDescription>
                    </CardHeader>
                    <CardContent class="space-y-6">
                        <div class="grid md:grid-cols-2 gap-6">
                            <div class="space-y-2">
                                <Label for="client_id">Cliente</Label>
                                <Popover v-model:open="open">
                                    <PopoverTrigger as-child>
                                        <Button variant="outline" role="combobox" class="w-full justify-between font-normal">
                                            {{ selectedClient ? selectedClient.name : "Selecione um cliente..." }}
                                            <ChevronsUpDown class="ml-2 h-4 w-4 shrink-0 opacity-50" />
                                        </Button>
                                    </PopoverTrigger>
                                    <PopoverContent class="w-[--radix-popover-trigger-width] p-0">
                                        <Command :filter-function="(list, query) => list">
                                            <CommandInput v-model="search" placeholder="Buscar cliente..."/>
                                            <CommandEmpty>Nenhum cliente encontrado.</CommandEmpty>
                                            <CommandList>
                                                <CommandGroup>
                                                    <CommandItem v-for="client in clientsList" :key="client.id" :value="client.name" @select="onClientSelect(client)">
                                                        <Check class="mr-2 h-4 w-4" :class="form.client_id !== client.id && 'opacity-0'" />
                                                        <div>
                                                            <p>{{ client.name }}</p>
                                                            <p class="text-xs text-muted-foreground">{{ client.document_number }}</p>
                                                        </div>
                                                    </CommandItem>
                                                </CommandGroup>
                                            </CommandList>
                                        </Command>
                                    </PopoverContent>
                                </Popover>
                                <p v-if="form.errors.client_id" class="text-sm text-red-600">{{ form.errors.client_id }}</p>
                            </div>
                            <div class="space-y-2">
                                <Label for="vehicle_id">Veículo</Label>
                                <Select v-model="form.vehicle_id" :disabled="!form.client_id || isLoadingVehicles">
                                    <SelectTrigger>
                                        <SelectValue placeholder="Selecione um veículo..." />
                                    </SelectTrigger>
                                    <SelectContent>
                                        <SelectItem v-for="vehicle in vehicles" :key="vehicle.id" :value="vehicle.id">
                                            {{ vehicle.make }} {{ vehicle.model }} - {{ vehicle.plate }}
                                        </SelectItem>
                                    </SelectContent>
                                </Select>
                                <p v-if="form.errors.vehicle_id" class="text-sm text-red-600">{{ form.errors.vehicle_id }}</p>
                            </div>
                        </div>
                        <div class="space-y-2">
                            <Label for="problem_reported">Problema Relatado pelo Cliente</Label>
                            <Textarea id="problem_reported" v-model="form.problem_reported" rows="4" />
                            <p v-if="form.errors.problem_reported" class="text-sm text-red-600">{{ form.errors.problem_reported }}</p>
                        </div>
                    </CardContent>
                    <CardFooter class="flex justify-end gap-2 border-t pt-6">
                        <Link :href="route('work-orders.index')">
                            <Button variant="outline">Cancelar</Button>
                        </Link>
                        <Button type="submit" :disabled="form.processing || !form.vehicle_id">
                            <LoaderCircle v-if="form.processing" class="h-4 w-4 animate-spin mr-2" />
                            Criar OS
                        </Button>
                    </CardFooter>
                </form>
            </Card>
        </div>
    </AuthenticatedLayout>
</template>
