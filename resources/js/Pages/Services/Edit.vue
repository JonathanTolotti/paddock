<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import ServiceForm from './Partials/ServiceForm.vue';
import { Head, Link } from '@inertiajs/vue3';
import { Button } from '@/components/ui/button';
import { ArrowLeft } from 'lucide-vue-next';
const props = defineProps({ service: Object });
</script>
<template>
    <Head title="Editar Serviço" />
    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center gap-4">
                <Link :href="route('services.index')"><Button variant="outline" size="icon" class="h-8 w-8"><ArrowLeft class="h-4 w-4" /></Button></Link>
                <h2 class="text-xl font-semibold">Editar Serviço: {{ service.name }}</h2>
            </div>
        </template>
        <div class="p-4 sm:p-6 lg:p-8 max-w-4xl mx-auto"><ServiceForm :service="service" /></div>
    </AuthenticatedLayout>
</template>
