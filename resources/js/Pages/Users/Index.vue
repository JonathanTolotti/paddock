<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, router } from '@inertiajs/vue3';
import Swal from 'sweetalert2';
import {
    Table,
    TableBody,
    TableCell,
    TableHead,
    TableHeader,
    TableRow,
} from '@/components/ui/table';
import {
    DropdownMenu,
    DropdownMenuContent,
    DropdownMenuItem,
    DropdownMenuTrigger,
} from '@/components/ui/dropdown-menu';
import { Button } from '@/components/ui/button';
import { PlusCircle, MoreHorizontal } from 'lucide-vue-next';

const confirmDelete = (userId) => {
    Swal.fire({
        title: 'Você tem certeza?',
        text: "O usuário será movido para a lixeira!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Sim, excluir!',
        cancelButtonText: 'Cancelar'
    }).then((result) => {
        if (result.isConfirmed) {
            router.delete(route('users.destroy', userId));
        }
    });
};

defineProps({
    users: {
        type: Object,
        required: true,
    }
});
</script>

<template>
    <Head title="Usuários" />
    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center justify-between w-full">
                <h2 class="text-xl font-semibold leading-tight">Gestão de Usuários</h2>
                <Link :href="route('users.create')">
                    <Button><PlusCircle class="w-4 h-4 mr-2" />Novo Usuário</Button>
                </Link>
            </div>
        </template>
        <div class="p-4 sm:p-6 lg:p-8">
            <div class="bg-background border shadow-sm rounded-lg">
                <Table>
                    <TableHeader>
                        <TableRow>
                            <TableHead>Nome</TableHead>
                            <TableHead>E-mail</TableHead>
                            <TableHead>Cargo</TableHead>
                            <TableHead class="w-[60px]"></TableHead>
                        </TableRow>
                    </TableHeader>
                    <TableBody>
                        <TableRow v-if="users.data.length === 0">
                            <TableCell :colspan="4" class="text-center py-16 text-muted-foreground">
                                Nenhum usuário encontrado.
                            </TableCell>
                        </TableRow>
                        <TableRow v-for="user in users.data" :key="user.id">
                            <TableCell class="font-medium">{{ user.name }}</TableCell>
                            <TableCell>{{ user.email }}</TableCell>
                            <TableCell>
                                <span v-if="user.roles.length > 0" class="capitalize">{{ user.roles[0].name }}</span>
                                <span v-else class="text-muted-foreground">Nenhum</span>
                            </TableCell>
                            <TableCell class="text-right">
                                <DropdownMenu>
                                    <DropdownMenuTrigger as-child>
                                        <Button variant="ghost" class="h-8 w-8 p-0">
                                            <MoreHorizontal class="h-4 w-4" />
                                        </Button>
                                    </DropdownMenuTrigger>
                                    <DropdownMenuContent align="end">
                                        <DropdownMenuItem as-child>
                                            <Link :href="route('users.edit', user.id)">Editar</Link>
                                        </DropdownMenuItem>
                                        <DropdownMenuItem @click="confirmDelete(user.id)" class="text-red-600 focus:text-red-600 cursor-pointer">
                                            Excluir
                                        </DropdownMenuItem>
                                    </DropdownMenuContent>
                                </DropdownMenu>
                            </TableCell>
                        </TableRow>
                    </TableBody>
                </Table>
            </div>
            <div v-if="users.last_page > 1" class="flex justify-center mt-6">
            </div>
        </div>
    </AuthenticatedLayout>
</template>
