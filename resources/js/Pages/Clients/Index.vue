<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, router } from '@inertiajs/vue3';
import { Mask } from 'maska';
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
import Swal from 'sweetalert2';

defineProps({
    clients: {
        type: Object,
        required: true,
    },
});

// 2. Crie as funções de formatação usando a API correta
const formatDocument = (client) => {
    if (!client || !client.document_number) return '';
    const maskPattern = client.document_type === 'CPF' ? '###.###.###-##' : '##.###.###/####-##';
    return new Mask({ mask: maskPattern }).masked(client.document_number);
};

const formatPhone = (phoneNumber) => {
    if (!phoneNumber) return '';
    // Para uso programático, escolhemos a máscara baseada no tamanho do número
    const maskPattern = phoneNumber.length > 10 ? '(##) #####-####' : '(##) ####-####';
    return new Mask({ mask: maskPattern }).masked(phoneNumber);
};

const confirmDelete = (clientUuid) => {
    Swal.fire({
        title: 'Você tem certeza?',
        text: "Esta ação não poderá ser revertida!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Sim, excluir!',
        cancelButtonText: 'Cancelar'
    }).then((result) => {
        if (result.isConfirmed) {
            router.delete(route('clients.destroy', clientUuid));
        }
    });
};
</script>

<template>
    <Head title="Clientes" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center justify-between w-full">
                <h2 class="text-xl font-semibold leading-tight">
                    Gestão de Clientes
                </h2>
                <Link v-if="$page.props.auth.user.permissions.includes('create_clients')" :href="route('clients.create')">
                    <Button>
                        <PlusCircle class="h-4 w-4 mr-2" />
                        Novo Cliente
                    </Button>
                </Link>
            </div>
        </template>

        <div class="p-4 sm:p-6 lg:p-8">
            <div class="bg-background border shadow-sm rounded-lg">
                <Table>
                    <TableHeader>
                        <TableRow>
                            <TableHead>Nome / Razão Social</TableHead>
                            <TableHead class="hidden md:table-cell">Documento</TableHead>
                            <TableHead class="hidden lg:table-cell">E-mail</TableHead>
                            <TableHead class="hidden md:table-cell">Telefone</TableHead>
                            <TableHead class="w-[60px]"></TableHead> </TableRow>
                    </TableHeader>
                    <TableBody>
                        <TableRow v-if="clients.data.length === 0">
                            <TableCell :colspan="5" class="text-center py-16 text-muted-foreground">
                                <p>Nenhum cliente encontrado.</p>
                                <Link :href="route('clients.create')" class="mt-2 inline-block">
                                    <Button variant="secondary">
                                        <PlusCircle class="h-4 w-4 mr-2" />
                                        Cadastrar Primeiro Cliente
                                    </Button>
                                </Link>
                            </TableCell>
                        </TableRow>
                        <TableRow v-for="client in clients.data" :key="client.uuid">
                            <TableCell class="font-medium">
                                <div class="font-bold">{{ client.name }}</div>
                                <div class="text-sm text-muted-foreground md:hidden">{{ formatPhone(client.phone_number) }}</div>
                            </TableCell>
                            <TableCell class="hidden md:table-cell">{{ formatDocument(client) }}</TableCell>
                            <TableCell class="hidden lg:table-cell">{{ client.email }}</TableCell>
                            <TableCell class="hidden md:table-cell">{{ formatPhone(client.phone_number) }}</TableCell>
                            <TableCell class="text-right">
                                <DropdownMenu>
                                    <DropdownMenuTrigger as-child>
                                        <Button variant="ghost" class="h-8 w-8 p-0">
                                            <span class="sr-only">Abrir menu</span>
                                            <MoreHorizontal class="h-4 w-4" />
                                        </Button>
                                    </DropdownMenuTrigger>
                                    <DropdownMenuContent align="end">
                                        <DropdownMenuItem v-if="$page.props.auth.user.permissions.includes('edit_clients')" as-child>
                                            <Link :href="route('clients.edit', client.uuid)">Editar</Link>
                                        </DropdownMenuItem>
                                        <DropdownMenuItem v-if="$page.props.auth.user.permissions.includes('delete_clients')" @click="confirmDelete(client.uuid)" class="text-red-600 focus:text-red-600 cursor-pointer">
                                            Excluir
                                        </DropdownMenuItem>
                                    </DropdownMenuContent>
                                </DropdownMenu>
                            </TableCell>
                        </TableRow>
                    </TableBody>
                </Table>
            </div>
            <div v-if="clients.links.length > 3" class="flex justify-center mt-6">
                <div class="flex flex-wrap -mb-1">
                    <template v-for="(link, key) in clients.links" :key="key">
                        <div v-if="link.url === null" class="mr-1 mb-1 px-4 py-3 text-sm leading-4 text-gray-400 border rounded" v-html="link.label" />
                        <Link v-else
                              class="mr-1 mb-1 px-4 py-3 text-sm leading-4 border rounded hover:bg-white focus:border-primary focus:text-primary"
                              :class="{ 'bg-primary text-white': link.active }"
                              :href="link.url"
                              v-html="link.label" />
                    </template>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
