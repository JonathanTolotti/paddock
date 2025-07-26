<script setup>
import { computed, ref } from 'vue';
import { Link, useForm } from '@inertiajs/vue3';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';
import { Textarea } from '@/components/ui/textarea';
import { Card, CardHeader, CardTitle, CardDescription, CardContent, CardFooter } from '@/components/ui/card';
import { LoaderCircle } from 'lucide-vue-next';
import axios from 'axios';

const props = defineProps({
    client: {
        type: Object,
        default: null,
    },
});

const form = useForm({
    _method: props.client ? 'PUT' : 'POST',
    name: props.client?.name || '',
    document_type: props.client?.document_type || 'CPF',
    document_number: props.client?.document_number || '',
    email: props.client?.email || '',
    phone_number: props.client?.phone_number || '',
    address_zip_code: props.client?.address_zip_code || '',
    address_street: props.client?.address_street || '',
    address_number: props.client?.address_number || '',
    address_complement: props.client?.address_complement || '',
    address_neighborhood: props.client?.address_neighborhood || '',
    address_city: props.client?.address_city || '',
    address_state: props.client?.address_state || '',
    notes: props.client?.notes || '',
});

const isEditMode = computed(() => !!props.client);

const documentMask = computed(() => {
    return form.document_type === 'CPF' ? '###.###.###-##' : '##.###.###/####-##';
});

const isFetchingCEP = ref(false);
const addressNumberInput = ref(null);

const fetchAddressFromCEP = async () => {
    const cep = form.address_zip_code.replace(/\D/g, '');
    if (cep.length !== 8) return;

    isFetchingCEP.value = true;
    try {
        const response = await axios.get(`https://viacep.com.br/ws/${cep}/json/`);
        if (!response.data.erro) {
            form.address_street = response.data.logradouro;
            form.address_neighborhood = response.data.bairro;
            form.address_city = response.data.localidade;
            form.address_state = response.data.uf;
        } else {
            form.address_street = '';
            form.address_neighborhood = '';
            form.address_city = '';
            form.address_state = '';
        }
    } catch (error) {
        console.error("Erro ao buscar CEP:", error);
    } finally {
        isFetchingCEP.value = false;
        addressNumberInput.value?.focus();
    }
};

const submit = () => {
    form.transform((data) => ({
        ...data,
        document_number: data.document_number ? data.document_number.replace(/\D/g, '') : '',
        phone_number: data.phone_number ? data.phone_number.replace(/\D/g, '') : '',
        address_zip_code: data.address_zip_code ? data.address_zip_code.replace(/\D/g, '') : '',
    })).post(isEditMode.value ? route('clients.update', props.client.uuid) : route('clients.store'));
};
</script>

<template>
    <Card>
        <form @submit.prevent="submit">
            <CardHeader>
                <CardTitle>Informações do Cliente</CardTitle>
                <CardDescription>Preencha os dados para {{ isEditMode ? 'atualizar' : 'cadastrar' }} um cliente no sistema.</CardDescription>
            </CardHeader>
            <CardContent class="space-y-8">
                <div class="space-y-4">
                    <h3 class="text-sm font-medium border-b pb-2">Dados Principais</h3>
                    <div class="grid md:grid-cols-2 gap-4">
                        <div class="space-y-2">
                            <Label for="name">Nome / Razão Social</Label>
                            <Input id="name" v-model="form.name" />
                            <p v-if="form.errors.name" class="text-sm text-red-600">{{ form.errors.name }}</p>
                        </div>
                        <div class="grid grid-cols-2 gap-4">
                            <div class="space-y-2">
                                <Label for="document_type">Tipo Doc.</Label>
                                <Select v-model="form.document_type">
                                    <SelectTrigger><SelectValue placeholder="Selecione" /></SelectTrigger>
                                    <SelectContent>
                                        <SelectItem value="CPF">CPF</SelectItem>
                                        <SelectItem value="CNPJ">CNPJ</SelectItem>
                                    </SelectContent>
                                </Select>
                            </div>
                            <div class="space-y-2">
                                <Label for="document_number">Número Doc.</Label>
                                <Input id="document_number" v-model="form.document_number" v-maska :data-maska="documentMask" />
                                <p v-if="form.errors.document_number" class="text-sm text-red-600">{{ form.errors.document_number }}</p>
                            </div>
                        </div>
                    </div>
                    <div class="grid md:grid-cols-2 gap-4">
                        <div class="space-y-2">
                            <Label for="email">E-mail</Label>
                            <Input id="email" type="email" v-model="form.email" />
                            <p v-if="form.errors.email" class="text-sm text-red-600">{{ form.errors.email }}</p>
                        </div>
                        <div class="space-y-2">
                            <Label for="phone_number">Telefone</Label>
                            <Input id="phone_number" v-model="form.phone_number" v-maska data-maska="['(##) ####-####', '(##) #####-####']" />
                            <p v-if="form.errors.phone_number" class="text-sm text-red-600">{{ form.errors.phone_number }}</p>
                        </div>
                    </div>
                </div>

                <div class="space-y-4">
                    <h3 class="text-sm font-medium border-b pb-2">Endereço</h3>
                    <div class="grid md:grid-cols-3 gap-4">
                        <div class="space-y-2 relative">
                            <Label for="address_zip_code">CEP</Label>
                            <Input id="address_zip_code" v-model="form.address_zip_code" v-maska data-maska="#####-###" @blur="fetchAddressFromCEP" />
                            <LoaderCircle v-if="isFetchingCEP" class="absolute right-3 top-8 h-5 w-5 animate-spin text-muted-foreground" />
                        </div>
                    </div>
                    <div class="grid md:grid-cols-3 gap-4">
                        <div class="space-y-2 md:col-span-2">
                            <Label for="address_street">Rua</Label>
                            <Input id="address_street" v-model="form.address_street" :disabled="isFetchingCEP" />
                        </div>
                        <div class="space-y-2">
                            <Label for="address_number">Número</Label>
                            <Input ref="addressNumberInput" id="address_number" v-model="form.address_number" :disabled="isFetchingCEP" />
                        </div>
                    </div>
                    <div class="grid md:grid-cols-2 gap-4">
                        <div class="space-y-2">
                            <Label for="address_neighborhood">Bairro</Label>
                            <Input id="address_neighborhood" v-model="form.address_neighborhood" :disabled="isFetchingCEP" />
                        </div>
                        <div class="space-y-2">
                            <Label for="address_complement">Complemento</Label>
                            <Input id="address_complement" v-model="form.address_complement" :disabled="isFetchingCEP" />
                        </div>
                    </div>
                    <div class="grid md:grid-cols-2 gap-4">
                        <div class="space-y-2">
                            <Label for="address_city">Cidade</Label>
                            <Input id="address_city" v-model="form.address_city" :disabled="isFetchingCEP" />
                        </div>
                        <div class="space-y-2">
                            <Label for="address_state">UF</Label>
                            <Input id="address_state" v-model="form.address_state" :disabled="isFetchingCEP" />
                        </div>
                    </div>
                </div>

                <div class="space-y-4">
                    <h3 class="text-sm font-medium border-b pb-2">Observações</h3>
                    <div class="space-y-2">
                        <Label for="notes">Notas Internas</Label>
                        <Textarea
                            id="notes"
                            v-model="form.notes"
                            placeholder="Qualquer observação relevante sobre o cliente..."
                            rows="4"
                        />
                        <p v-if="form.errors.notes" class="text-sm text-red-600">{{ form.errors.notes }}</p>
                    </div>
                </div>

            </CardContent>
            <CardFooter class="flex justify-end gap-2 border-t pt-6">
                <Link :href="route('clients.index')">
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
