<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import ServiceForm from './Partials/ServiceForm.vue';
import { Head, Link } from '@inertiajs/vue3';
import { Button } from '@/components/ui/button';
import { ArrowLeft } from 'lucide-vue-next';
</script>
<template>
    <Head title="Novo Serviço" />
    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center gap-4">
                <Link :href="route('services.index')"><Button variant="outline" size="icon" class="h-8 w-8"><ArrowLeft class="h-4 w-4" /></Button></Link>
                <h2 class="text-xl font-semibold">Cadastrar Novo Serviço</h2>
            </div>
        </template>
        <div class="p-4 sm:p-6 lg:p-8 max-w-4xl mx-auto"><ServiceForm /></div>
    </AuthenticatedLayout>
</template>
