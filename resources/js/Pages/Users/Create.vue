<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import UserForm from './Partials/UserForm.vue';
import { Head, Link } from '@inertiajs/vue3';
import { Button } from '@/components/ui/button';
import { ArrowLeft } from 'lucide-vue-next';

const props = defineProps({
    roles: Array
});
</script>
<template>
    <Head title="Novo Usuário" />
    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center gap-4">
                <Link :href="route('users.index')">
                    <Button variant="outline" size="icon" class="h-8 w-8">
                        <ArrowLeft class="h-4 w-4" />
                    </Button>
                </Link>
                <h2 class="text-xl font-semibold leading-tight">Cadastrar Novo Usuário</h2>
            </div>
        </template>
        <div class="p-4 sm:p-6 lg:p-8 max-w-4xl mx-auto">
            <UserForm :roles="roles" />
        </div>
    </AuthenticatedLayout>
</template>
