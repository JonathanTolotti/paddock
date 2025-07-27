<script setup>
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Textarea } from '@/components/ui/textarea';
import { Card, CardHeader, CardTitle, CardDescription, CardContent, CardFooter } from '@/components/ui/card';
import { LoaderCircle } from 'lucide-vue-next';
import { Link, useForm } from '@inertiajs/vue3';
import { computed } from 'vue';

const props = defineProps({
    part: {
        type: Object,
        default: null,
    },
});

const form = useForm({
    _method: props.part ? 'PUT' : 'POST',
    name: props.part?.name || '',
    sku: props.part?.sku || '',
    brand: props.part?.brand || '',
    description: props.part?.description || '',
    cost_price: props.part?.cost_price || 0,
    sale_price: props.part?.sale_price || 0,
    quantity: props.part?.quantity || 0,
});

const isEditMode = computed(() => !!props.part);

const submit = () => {
    form.post(isEditMode.value ? route('parts.update', props.part.uuid) : route('parts.store'));
};
</script>

<template>
    <Card>
        <form @submit.prevent="submit">
            <CardHeader>
                <CardTitle>Dados da Peça</CardTitle>
                <CardDescription>
                    Preencha as informações da peça para o catálogo.
                </CardDescription>
            </CardHeader>
            <CardContent class="space-y-6">
                <div class="grid md:grid-cols-2 gap-6">
                    <div class="space-y-2">
                        <Label for="name">Nome da Peça</Label>
                        <Input id="name" v-model="form.name" />
                        <p v-if="form.errors.name" class="text-sm text-red-600">{{ form.errors.name }}</p>
                    </div>
                    <div class="space-y-2">
                        <Label for="brand">Marca</Label>
                        <Input id="brand" v-model="form.brand" />
                        <p v-if="form.errors.brand" class="text-sm text-red-600">{{ form.errors.brand }}</p>
                    </div>
                </div>
                <div class="space-y-2">
                    <Label for="sku">SKU (Código)</Label>
                    <Input id="sku" v-model="form.sku" />
                    <p v-if="form.errors.sku" class="text-sm text-red-600">{{ form.errors.sku }}</p>
                </div>
                <div class="grid md:grid-cols-3 gap-6">
                    <div class="space-y-2">
                        <Label for="cost_price">Preço de Custo (R$)</Label>
                        <Input id="cost_price" v-model="form.cost_price" type="number" step="0.01" />
                        <p v-if="form.errors.cost_price" class="text-sm text-red-600">{{ form.errors.cost_price }}</p>
                    </div>
                    <div class="space-y-2">
                        <Label for="sale_price">Preço de Venda (R$)</Label>
                        <Input id="sale_price" v-model="form.sale_price" type="number" step="0.01" />
                        <p v-if="form.errors.sale_price" class="text-sm text-red-600">{{ form.errors.sale_price }}</p>
                    </div>
                    <div class="space-y-2">
                        <Label for="quantity">Estoque Atual</Label>
                        <Input id="quantity" v-model="form.quantity" type="number" />
                        <p v-if="form.errors.quantity" class="text-sm text-red-600">{{ form.errors.quantity }}</p>
                    </div>
                </div>
                <div class="space-y-2">
                    <Label for="description">Descrição</Label>
                    <Textarea id="description" v-model="form.description" rows="4" />
                    <p v-if="form.errors.description" class="text-sm text-red-600">{{ form.errors.description }}</p>
                </div>
            </CardContent>
            <CardFooter class="flex justify-end gap-2 border-t pt-6">
                <Link :href="route('parts.index')">
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
