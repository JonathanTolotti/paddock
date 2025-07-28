<script setup>
import { Button } from '@/components/ui/button/index.js';
import { Input } from '@/components/ui/input/index.js';
import { Label } from '@/components/ui/label/index.js';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select/index.js';
import { Card, CardHeader, CardTitle, CardDescription, CardContent, CardFooter } from '@/components/ui/card/index.js';
import { LoaderCircle } from 'lucide-vue-next';
import { Link, useForm } from '@inertiajs/vue3';
import { computed } from 'vue';

const props = defineProps({
    user: { type: Object, default: null },
    roles: Array,
});

const form = useForm({
    _method: props.user ? 'PUT' : 'POST',
    name: props.user?.name || '',
    email: props.user?.email || '',
    role: props.user?.roles[0]?.name || null,
    password: '',
    password_confirmation: '',
});
const isEditMode = computed(() => !!props.user);
const submit = () => {
    form.post(isEditMode.value ? route('users.update', props.user.id) : route('users.store'));
};
</script>
<template>
    <Card>
        <form @submit.prevent="submit">
            <CardHeader>
                <CardTitle>Dados do Usuário</CardTitle>
            </CardHeader>
            <CardContent class="space-y-6">
                <div class="grid md:grid-cols-2 gap-6">
                    <div class="space-y-2 md:col-span-2">
                        <Label for="name">Nome Completo</Label>
                        <Input id="name" v-model="form.name" />
                        <p v-if="form.errors.name" class="text-sm text-red-600">{{ form.errors.name }}</p>
                    </div>

                    <div class="space-y-2">
                        <Label for="email">E-mail</Label>
                        <Input id="email" v-model="form.email" type="email" />
                        <p v-if="form.errors.email" class="text-sm text-red-600">{{ form.errors.email }}</p>
                    </div>

                    <div class="space-y-2">
                        <Label for="role">Cargo</Label>
                        <Select v-model="form.role">
                            <SelectTrigger><SelectValue placeholder="Selecione um cargo..." /></SelectTrigger>
                            <SelectContent>
                                <SelectItem v-for="role in roles" :key="role.id" :value="role.name">
                                    {{ role.name }}
                                </SelectItem>
                            </SelectContent>
                        </Select>
                        <p v-if="form.errors.role" class="text-sm text-red-600">{{ form.errors.role }}</p>
                    </div>

                    <div class="space-y-2">
                        <Label for="password">Senha</Label>
                        <Input id="password" v-model="form.password" type="password" :placeholder="isEditMode ? 'Deixe em branco para não alterar' : ''" />
                        <p v-if="form.errors.password" class="text-sm text-red-600">{{ form.errors.password }}</p>
                    </div>

                    <div class="space-y-2">
                        <Label for="password_confirmation">Confirmar Senha</Label>
                        <Input id="password_confirmation" v-model="form.password_confirmation" type="password" />
                    </div>
                </div>
            </CardContent>
            <CardFooter class="flex justify-end gap-2 border-t pt-6">
                <Link :href="route('users.index')">
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
