<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import { Button } from '@/components/ui/button';
import { Card, CardHeader, CardTitle, CardDescription, CardContent, CardFooter } from '@/components/ui/card';
import { Checkbox } from '@/components/ui/checkbox';
import { Label } from '@/components/ui/label';
import { ArrowLeft, LoaderCircle } from 'lucide-vue-next';

const props = defineProps({
    role: Object,
    allPermissions: Object,
    rolePermissions: Array,
});

const form = useForm({
    _method: 'PUT',
    permissions: props.rolePermissions,
});

const submit = () => {
    form.post(route('roles.update', props.role.id));
};
</script>

<template>
    <Head :title="`Editar Cargo: ${role.name}`" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center gap-4">
                <Link :href="route('roles.index')">
                    <Button variant="outline" size="icon" class="h-8 w-8">
                        <ArrowLeft class="h-4 w-4" />
                    </Button>
                </Link>
                <h2 class="text-xl font-semibold leading-tight">
                    Editar Cargo: <span class="capitalize">{{ role.name }}</span>
                </h2>
            </div>
        </template>

        <div class="p-4 sm:p-6 lg:p-8 max-w-4xl mx-auto">
            <Card>
                <form @submit.prevent="submit">
                    <CardHeader>
                        <CardTitle>Permissões</CardTitle>
                        <CardDescription>Selecione as permissões que este cargo terá acesso.</CardDescription>
                    </CardHeader>
                    <CardContent class="space-y-6">
                        <div v-for="(permissions, group) in allPermissions" :key="group" class="space-y-4">
                            <h3 class="font-semibold capitalize border-b pb-2">{{ group }}</h3>
                            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                                <div v-for="permission in permissions" :key="permission.id" class="flex items-center space-x-2">
                                    <Checkbox
                                        :id="`permission-${permission.id}`"
                                        :value="permission.name"
                                        v-model:checked="form.permissions"
                                    />
                                    <Label :for="`permission-${permission.id}`" class="text-sm font-normal">
                                        {{ permission.name.split('_')[0] }} </Label>
                                </div>
                            </div>
                        </div>
                    </CardContent>
                    <CardFooter class="flex justify-end border-t pt-6">
                        <Button type="submit" :disabled="form.processing">
                            <LoaderCircle v-if="form.processing" class="h-4 w-4 animate-spin mr-2" />
                            Salvar Alterações
                        </Button>
                    </CardFooter>
                </form>
            </Card>
        </div>
    </AuthenticatedLayout>
</template>
