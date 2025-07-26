<script setup>
import { ref, onMounted } from 'vue';
import { Head, Link, router } from '@inertiajs/vue3';
import { Sun, Moon, Menu, X, Home, Wrench, Users, Car, Warehouse } from 'lucide-vue-next';
import {
    DropdownMenu,
    DropdownMenuContent,
    DropdownMenuItem,
    DropdownMenuLabel,
    DropdownMenuSeparator,
    DropdownMenuTrigger,
} from '@/components/ui/dropdown-menu';
import { Button } from '@/components/ui/button';

defineProps({
    title: String,
});

// --- Estados ---
const isMobileMenuOpen = ref(false);
const isDarkMode = ref(false);

// --- Lógica do Dark Mode ---
const toggleDarkMode = () => {
    isDarkMode.value = !isDarkMode.value;
    if (isDarkMode.value) {
        document.documentElement.classList.add('dark');
        localStorage.setItem('darkMode', 'enabled');
    } else {
        document.documentElement.classList.remove('dark');
        localStorage.setItem('darkMode', 'disabled');
    }
};
onMounted(() => {
    if (localStorage.getItem('darkMode') === 'enabled' ||
        (window.matchMedia('(prefers-color-scheme: dark)').matches && localStorage.getItem('darkMode') !== 'disabled')) {
        isDarkMode.value = true;
    }
});

// --- Funções ---
const logout = () => {
    router.post(route('logout'));
};

// --- Dados de Navegação ---
const menuItems = [
    { name: 'Dashboard', routeName: 'dashboard', icon: Home },
    { name: 'Ordens de Serviço', routeName: '#', icon: Wrench },
    { name: 'Clientes', routeName: '#', icon: Users },
    { name: 'Veículos', routeName: '#', icon: Car },
    { name: 'Estoque', routeName: '#', icon: Warehouse },
];
</script>

<template>
    <Head :title="title" />
    <div class="min-h-screen bg-gray-100 dark:bg-zinc-900 text-gray-900 dark:text-gray-100">
        <header class="bg-white dark:bg-zinc-950 border-b border-gray-200 dark:border-zinc-800 sticky top-0 z-40">
            <div class="container mx-auto px-4">
                <div class="flex h-16 items-center justify-between">
                    <div class="flex items-center gap-6">
                        <Link :href="route('dashboard')" class="text-xl font-bold">
                            Paddock
                        </Link>
                        <nav class="hidden md:flex items-center gap-5 text-sm font-medium text-muted-foreground">
                            <Link v-for="item in menuItems" :key="item.name" :href="item.routeName === '#' ? '#' : route(item.routeName)"
                                  :class="{'text-primary font-semibold': item.routeName !== '#' && route().current(item.routeName)}"
                                  class="transition-colors hover:text-primary">
                                {{ item.name }}
                            </Link>
                        </nav>
                    </div>

                    <div class="flex items-center gap-4">
                        <Button @click="toggleDarkMode" variant="ghost" size="icon">
                            <Sun class="h-[1.2rem] w-[1.2rem] rotate-0 scale-100 transition-all dark:-rotate-90 dark:scale-0" />
                            <Moon class="absolute h-[1.2rem] w-[1.2rem] rotate-90 scale-0 transition-all dark:rotate-0 dark:scale-100" />
                        </Button>

                        <DropdownMenu>
                            <DropdownMenuTrigger as-child>
                                <Button variant="ghost" class="relative h-8 w-8 rounded-full">
                                    <img src="https://ui.shadcn.com/avatars/01.png" class="h-8 w-8 rounded-full" />
                                </Button>
                            </DropdownMenuTrigger>
                            <DropdownMenuContent align="end" class="w-56">
                                <DropdownMenuLabel>Minha Conta</DropdownMenuLabel>
                                <DropdownMenuSeparator />
                                <DropdownMenuItem as-child>
                                    <Link :href="route('profile.edit')" class="w-full">Perfil</Link>
                                </DropdownMenuItem>
                                <DropdownMenuItem @click="logout" class="cursor-pointer">
                                    Sair
                                </DropdownMenuItem>
                            </DropdownMenuContent>
                        </DropdownMenu>

                        <button @click="isMobileMenuOpen = !isMobileMenuOpen" class="md:hidden">
                            <Menu v-if="!isMobileMenuOpen" class="h-6 w-6" />
                            <X v-else class="h-6 w-6" />
                        </button>
                    </div>
                </div>
            </div>

            <nav v-if="isMobileMenuOpen" class="md:hidden border-t border-gray-200 dark:border-zinc-800 py-4">
                <div class="container mx-auto px-4 flex flex-col space-y-2">
                    <Link v-for="item in menuItems" :key="item.name" :href="item.routeName === '#' ? '#' : route(item.routeName)"
                          @click="isMobileMenuOpen = false"
                          :class="{'text-primary font-semibold': item.routeName !== '#' && route().current(item.routeName)}"
                          class="flex items-center gap-3 rounded-lg px-3 py-2 text-base font-medium text-muted-foreground transition-all hover:text-primary">
                        <component :is="item.icon" class="h-5 w-5" />
                        {{ item.name }}
                    </Link>
                </div>
            </nav>
        </header>

        <main class="py-10">
            <div class="container mx-auto px-4">
                <header v-if="$slots.header" class="mb-8">
                    <slot name="header" />
                </header>

                <slot />
            </div>
        </main>
    </div>
</template>
