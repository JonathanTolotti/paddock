<script setup>
import { ref, onMounted, computed, watchEffect } from 'vue';
import { Head, Link, router, usePage } from '@inertiajs/vue3';
import { Button } from '@/components/ui/button' // Adicione a importação do Button
import {
    DropdownMenu,
    DropdownMenuContent,
    DropdownMenuItem,
    DropdownMenuTrigger,
} from '@/components/ui/dropdown-menu';
import { Home, Wrench, Users, Car, Warehouse, Sun, Moon, MoreHorizontal, PlusCircle } from 'lucide-vue-next';
import Swal from 'sweetalert2';

defineProps({
    title: String,
});


const isDarkMode = ref(
    localStorage.getItem('theme') === 'dark' ||
    (!('theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)
);

watchEffect(() => {
    if (isDarkMode.value) {
        document.documentElement.classList.add('dark');
        localStorage.setItem('theme', 'dark');
    } else {
        document.documentElement.classList.remove('dark');
        localStorage.setItem('theme', 'light');
    }
});

const toggleDarkMode = () => {
    isDarkMode.value = !isDarkMode.value;
};


const logout = () => {
    router.post(route('logout'));
};

const menuItems = [
    { name: 'Dashboard', routeName: 'dashboard', icon: 'Home' },
    { name: 'Ordens de Serviço', routeName: '#', icon: 'Wrench' },
    { name: 'Clientes', routeName: 'clients.index', icon: 'Users' },
    { name: 'Veículos', routeName: '#', icon: 'Car' },
    { name: 'Estoque', routeName: '#', icon: 'Warehouse' },
];

const icons = {
    Home, Wrench, Users, Car, Warehouse
};

watchEffect(() => {
    const page = usePage();
    if (page.props.flash?.success) {
        Swal.fire({
            icon: 'success',
            title: page.props.flash.success,
            showConfirmButton: false,
            timer: 2000,
            toast: true,
            position: 'top-end',
            timerProgressBar: true,
        });
    }

    if (page.props.flash?.error) {
        Swal.fire({
            icon: 'error',
            title: page.props.flash.error,
            showConfirmButton: false,
            timer: 3000,
            toast: true,
            position: 'top-end',
            timerProgressBar: true,
        });
    }
});
</script>

<template>
    <Head :title="title" />
    <div class="grid min-h-screen w-full md:grid-cols-[220px_1fr] lg:grid-cols-[280px_1fr] dark:bg-zinc-950">
        <aside class="hidden border-r bg-muted/40 md:block dark:bg-zinc-900/40">
            <div class="flex h-full max-h-screen flex-col gap-2">
                <div class="flex h-14 items-center border-b px-4 lg:h-[60px] lg:px-6">
                    <Link :href="route('dashboard')" class="flex items-center gap-2 font-semibold">
                        <span class="text-xl">Paddock</span>
                    </Link>
                </div>
                <div class="flex-1">
                    <nav class="grid items-start px-2 text-sm font-medium lg:px-4">
                        <Link v-for="item in menuItems" :key="item.name" :href="item.routeName === '#' ? '#' : route(item.routeName)"
                              :class="{'bg-muted dark:bg-zinc-800 text-primary dark:text-zinc-50': item.routeName !== '#' && route().current(item.routeName)}"
                              class="flex items-center gap-3 rounded-lg px-3 py-2 text-muted-foreground transition-all hover:text-primary dark:text-zinc-400 dark:hover:text-zinc-50"
                        >
                            <component :is="icons[item.icon]" class="h-4 w-4" />
                            {{ item.name }}
                        </Link>
                    </nav>
                </div>
            </div>
        </aside>

        <div class="flex flex-col">
            <header class="flex h-14 items-center gap-4 border-b bg-muted/40 px-4 lg:h-[60px] lg:px-6 dark:bg-zinc-900/40">
                <button @click="isMobileMenuOpen = true" class="md:hidden">
                    <PanelLeft class="h-6 w-6" />
                </button>
                <div class="w-full flex-1">
                </div>
                <Button @click="toggleDarkMode" variant="ghost" size="icon">
                    <Sun class="h-[1.2rem] w-[1.2rem] rotate-0 scale-100 transition-all dark:-rotate-90 dark:scale-0" />
                    <Moon class="absolute h-[1.2rem] w-[1.2rem] rotate-90 scale-0 transition-all dark:rotate-0 dark:scale-100" />
                    <span class="sr-only">Alternar tema</span>
                </Button>

                <DropdownMenu>
                    <DropdownMenuTrigger as-child>
                        <button class="relative">
                            <img src="https://ui.shadcn.com/avatars/01.png" class="h-8 w-8 rounded-full" />
                        </button>
                    </DropdownMenuTrigger>
                    <DropdownMenuContent align="end">
                        <DropdownMenuLabel>{{ $page.props.auth.user.name }}</DropdownMenuLabel>
                        <DropdownMenuSeparator />
                        <DropdownMenuItem as-child>
                            <Link :href="route('profile.edit')">Perfil</Link>
                        </DropdownMenuItem>
                        <DropdownMenuItem @click="logout" class="cursor-pointer">
                            Sair
                        </DropdownMenuItem>
                    </DropdownMenuContent>
                </DropdownMenu>

            </header>

            <main class="flex flex-1 flex-col gap-4 p-4 lg:gap-6 lg:p-6">
                <header v-if="$slots.header" class="flex items-center">
                    <slot name="header" />
                </header>
                <slot />
            </main>
        </div>
    </div>
</template>
