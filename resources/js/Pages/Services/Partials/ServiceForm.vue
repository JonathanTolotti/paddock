<script setup>
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Textarea } from '@/components/ui/textarea';
import { Card, CardHeader, CardTitle, CardDescription, CardContent, CardFooter } from '@/components/ui/card';
import { LoaderCircle } from 'lucide-vue-next';
import { Link, useForm } from '@inertiajs/vue3';
import { computed } from 'vue';

const props = defineProps({ service: { type: Object, default: null } });
const form = useForm({
    _method: props.service ? 'PUT' : 'POST',
    name: props.service?.name || '',
    description: props.service?.description || '',
    price: props.service?.price || 0,
});
const isEditMode = computed(() => !!props.service);
const submit = () => {
    form.post(isEditMode.value ? route('services.update', props.service.uuid) : route('services.store'));
};
</script>
<template>
    <Card>
        <form @submit.prevent="submit">
            <CardHeader>
                <CardTitle>Dados do Serviço</CardTitle>
            </CardHeader>
            <CardContent class="space-y-6">
                <div class="grid md:grid-cols-3 gap-6">
                    <div class="space-y-2 md:col-span-2">
                        <Label for="name">Nome do Serviço</Label>
                        <Input id="name" v-model="form.name" />
                        <p v-if="form.errors.name" class="text-sm text-red-600">{{ form.errors.name }}</p>
                    </div>
                    <div class="space-y-2">
                        <Label for="price">Preço Padrão (R$)</Label>
                        <Input id="price" v-model="form.price" type="number" step="0.01" />
                        <p v-if="form.errors.price" class="text-sm text-red-600">{{ form.errors.price }}</p>
                    </div>
                </div>
                <div class="space-y-2">
                    <Label for="description">Descrição</Label>
                    <Textarea id="description" v-model="form.description" rows="4" />
                    <p v-if="form.errors.description" class="text-sm text-red-600">{{ form.errors.description }}</p>
                </div>
            </CardContent>
            <CardFooter class="flex justify-end gap-2 border-t pt-6">
                <Link :href="route('services.index')">
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
