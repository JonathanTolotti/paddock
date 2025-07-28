<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, useForm } from '@inertiajs/vue3';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Card, CardHeader, CardTitle, CardDescription, CardContent, CardFooter } from '@/components/ui/card';
import { LoaderCircle } from 'lucide-vue-next';
import { ref } from 'vue';

const props = defineProps({
    settings: {
        type: Object,
        default: () => ({}),
    },
});

const form = useForm({
    workshop_name: props.settings.workshop_name || '',
    workshop_document: props.settings.workshop_document || '',
    workshop_phone: props.settings.workshop_phone || '',
    workshop_address: props.settings.workshop_address || '',
    workshop_logo: null, // O input de arquivo é controlado separadamente
});

const logoPreview = ref(props.settings.workshop_logo_url || null);

function updateLogoPreview(event) {
    const file = event.target.files[0];
    if (file) {
        form.workshop_logo = file;
        logoPreview.value = URL.createObjectURL(file);
    }
}

const submit = () => {
    form.transform((data) => ({
        ...data,
        workshop_document: data.workshop_document ? data.workshop_document.replace(/\D/g, '') : '',
        workshop_phone: data.workshop_phone ? data.workshop_phone.replace(/\D/g, '') : '',
    })).post(route('settings.store'), {
        preserveState: true,
    });
};
</script>

<template>
    <Head title="Configurações" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight">
                Configurações da Oficina
            </h2>
        </template>

        <div class="p-4 sm:p-6 lg:p-8 max-w-4xl mx-auto">
            <Card>
                <form @submit.prevent="submit">
                    <CardHeader>
                        <CardTitle>Dados da Empresa</CardTitle>
                        <CardDescription>
                            Estas informações aparecerão em documentos impressos, como Ordens de Serviço.
                        </CardDescription>
                    </CardHeader>
                    <CardContent class="space-y-6">
                        <div class="grid md:grid-cols-2 gap-6">
                            <div class="space-y-2">
                                <Label for="workshop_name">Nome da Oficina</Label>
                                <Input id="workshop_name" v-model="form.workshop_name" />
                                <p v-if="form.errors.workshop_name" class="text-sm text-red-600">{{ form.errors.workshop_name }}</p>
                            </div>
                            <div class="space-y-2">
                                <Label for="workshop_document">CNPJ</Label>
                                <Input
                                    id="workshop_document"
                                    v-model="form.workshop_document"
                                    v-maska
                                    data-maska="##.###.###/####-##"
                                />
                                <p v-if="form.errors.workshop_document" class="text-sm text-red-600">{{ form.errors.workshop_document }}</p>
                            </div>
                        </div>
                        <div class="grid md:grid-cols-2 gap-6">
                            <div class="space-y-2">
                                <Label for="workshop_phone">Telefone</Label>
                                <Input
                                    id="workshop_phone"
                                    v-model="form.workshop_phone"
                                    v-maska
                                    data-maska="['(##) ####-####', '(##) #####-####']"
                                />
                                <p v-if="form.errors.workshop_phone" class="text-sm text-red-600">{{ form.errors.workshop_phone }}</p>
                            </div>
                            <div class="space-y-2">
                                <Label for="workshop_address">Endereço</Label>
                                <Input id="workshop_address" v-model="form.workshop_address" />
                                <p v-if="form.errors.workshop_address" class="text-sm text-red-600">{{ form.errors.workshop_address }}</p>
                            </div>
                        </div>
                        <div class="space-y-2">
                            <Label for="workshop_logo">Logo da Oficina</Label>
                            <div class="flex items-center gap-4">
                                <img v-if="logoPreview" :src="logoPreview" alt="Pré-visualização do Logo" class="h-20 w-20 object-cover rounded-md border">
                                <div v-else class="h-20 w-20 bg-muted rounded-md flex items-center justify-center text-muted-foreground text-sm">Sem Logo</div>
                                <Input id="workshop_logo" @input="updateLogoPreview" type="file" class="block" />
                            </div>
                            <p v-if="form.errors.workshop_logo" class="text-sm text-red-600">{{ form.errors.workshop_logo }}</p>
                        </div>
                    </CardContent>
                    <CardFooter class="flex justify-end border-t pt-6">
                        <Button type="submit" :disabled="form.processing">
                            <LoaderCircle v-if="form.processing" class="h-4 w-4 animate-spin mr-2" />
                            Salvar Configurações
                        </Button>
                    </CardFooter>
                </form>
            </Card>
        </div>
    </AuthenticatedLayout>
</template>
