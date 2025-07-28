<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link } from '@inertiajs/vue3';
import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from '@/components/ui/table';
import { Button } from '@/components/ui/button';

defineProps({
    roles: {
        type: Array,
        required: true,
    }
});
</script>

<template>
    <Head title="Cargos e Permissões" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight">
                Gestão de Cargos e Permissões
            </h2>
        </template>

        <div class="p-4 sm:p-6 lg:p-8">
            <div class="bg-background border shadow-sm rounded-lg">
                <Table>
                    <TableHeader>
                        <TableRow>
                            <TableHead>Cargo</TableHead>
                            <TableHead class="text-right">Ações</TableHead>
                        </TableRow>
                    </TableHeader>
                    <TableBody>
                        <TableRow v-for="role in roles" :key="role.id">
                            <TableCell class="font-medium capitalize">{{ role.name }}</TableCell>
                            <TableCell class="text-right">
                                <Link v-if="role.name !== 'Admin'" :href="route('roles.edit', role.id)">
                                    <Button variant="outline">
                                        Editar Permissões
                                    </Button>
                                </Link>
                            </TableCell>
                        </TableRow>
                    </TableBody>
                </Table>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
