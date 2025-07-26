<script setup>
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Card, CardHeader, CardTitle, CardDescription, CardContent, CardFooter } from '@/components/ui/card';
import { LoaderCircle, Check, ChevronsUpDown, ArrowLeft } from 'lucide-vue-next';
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
import { Link, useForm } from '@inertiajs/vue3';
import { ref, watch, computed } from 'vue';
import axios from 'axios';
import { Textarea } from '@/components/ui/textarea';


const props = defineProps({
    clients: Array,
    vehicle: {
        type: Object,
        default: null,
    },
});

const form = useForm({
    _method: props.vehicle ? 'PUT' : 'POST',
    client_id: props.vehicle?.client_id || null,
    plate: props.vehicle?.plate || '',
    make: props.vehicle?.make || '',
    model: props.vehicle?.model || '',
    year: props.vehicle?.year || '',
    color: props.vehicle?.color || '',
    vin: props.vehicle?.vin || '',
    notes: props.vehicle?.notes || '',
});

const isEditMode = computed(() => !!props.vehicle);

// --- Lógica do Combobox de Clientes ---
const clientsList = ref(props.clients);
const open = ref(false);
const selectedClient = ref(
    props.vehicle
        ? props.clients.find(c => c.id === props.vehicle.client_id)
        : null
);
const search = ref('');
let debounceTimer = null;

const searchClients = async (query) => {
    try {
        const response = await axios.get(route('clients.search', { q: query }));
        clientsList.value = response.data;
    } catch (error) { console.error('Erro ao buscar clientes:', error); }
};

watch(search, (newValue) => {
    clearTimeout(debounceTimer);
    debounceTimer = setTimeout(() => { searchClients(newValue); }, 300);
});

function onClientSelect(client) {
    selectedClient.value = client;
    form.client_id = client.id;
    open.value = false;
}
// --- Fim da Lógica do Combobox ---

// --- Lógica da Máscara da Placa ---
const plateMask = computed(() => {
    const raw = form.plate.replace(/[^a-zA-Z0-9]/g, '');
    const isMercosul = /^[A-Z]{3}[0-9][A-Z]/.test(raw);
    return isMercosul ? 'AAA#A##' : 'AAA-####';
});

const plateMaskOptions = {
    mask: plateMask,
    tokens: {
        A: { pattern: /[a-zA-Z]/, uppercase: true },
        '#': { pattern: /\d/ },
    },
};
// --- Fim da Lógica da Máscara ---

const submit = () => {
    form.transform((data) => ({
        ...data,
        plate: data.plate ? data.plate.toUpperCase().replace(/[\s-]/g, '') : '',
    })).post(isEditMode.value ? route('vehicles.update', props.vehicle.uuid) : route('vehicles.store'));
};
</script>

<template>
    <Card>
        <form @submit.prevent="submit">
            <CardHeader>
                <CardTitle>Informações do Veículo</CardTitle>
                <CardDescription>
                    Preencha os dados para {{ isEditMode ? 'atualizar' : 'cadastrar' }} um veículo no sistema.
                </CardDescription>
            </CardHeader>
            <CardContent class="grid md:grid-cols-2 gap-x-8 gap-y-6">
                <div class="space-y-2 md:col-span-2">
                    <Label for="client_id">Proprietário</Label>
                    <Popover v-model:open="open">
                        <PopoverTrigger as-child>
                            <Button variant="outline" role="combobox" class="w-full md:w-[500px] justify-between font-normal">
                                {{ selectedClient ? selectedClient.name : "Selecione um cliente..." }}
                                <ChevronsUpDown class="ml-2 h-4 w-4 shrink-0 opacity-50" />
                            </Button>
                        </PopoverTrigger>
                        <PopoverContent class="w-full md:w-[500px] p-0">
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
                    <Label for="plate">Placa</Label>
                    <Input id="plate" v-model="form.plate" v-maska="plateMaskOptions" />
                    <p v-if="form.errors.plate" class="text-sm text-red-600">{{ form.errors.plate }}</p>
                </div>
                <div class="space-y-2">
                    <Label for="vin">Chassi (VIN)</Label>
                    <Input id="vin" v-model="form.vin" />
                    <p v-if="form.errors.vin" class="text-sm text-red-600">{{ form.errors.vin }}</p>
                </div>
                <div class="space-y-2">
                    <Label for="make">Marca</Label>
                    <Input id="make" v-model="form.make" />
                    <p v-if="form.errors.make" class="text-sm text-red-600">{{ form.errors.make }}</p>
                </div>
                <div class="space-y-2">
                    <Label for="model">Modelo</Label>
                    <Input id="model" v-model="form.model" />
                    <p v-if="form.errors.model" class="text-sm text-red-600">{{ form.errors.model }}</p>
                </div>
                <div class="space-y-2">
                    <Label for="year">Ano</Label>
                    <Input id="year" v-model="form.year" type="number" />
                    <p v-if="form.errors.year" class="text-sm text-red-600">{{ form.errors.year }}</p>
                </div>
                <div class="space-y-2">
                    <Label for="color">Cor</Label>
                    <Input id="color" v-model="form.color" />
                    <p v-if="form.errors.color" class="text-sm text-red-600">{{ form.errors.color }}</p>
                </div>
                <div class="space-y-2 md:col-span-2">
                    <Label for="notes">Observações</Label>
                    <Textarea
                        id="notes"
                        v-model="form.notes"
                        placeholder="Informações adicionais sobre o veículo..."
                        rows="4"
                    />
                    <p v-if="form.errors.notes" class="text-sm text-red-600">{{ form.errors.notes }}</p>
                </div>
            </CardContent>
            <CardFooter class="flex justify-end gap-2 border-t pt-6">
                <Link :href="route('vehicles.index')">
                    <Button type="button" variant="outline">Cancelar</Button>
                </Link>
                <Button type="submit" :disabled="form.processing">
                    <LoaderCircle v-if="form.processing" class="h-4 w-4 animate-spin mr-2" />
                    Salvar
                </Button>
            </CardFooter>
        </form>
    </Card>
</template>
