<script setup>
import { ref, onMounted } from 'vue';
import { Head, Link, router } from '@inertiajs/vue3';

// Importando componentes do shadcn-vue
import {
    DropdownMenu,
    DropdownMenuContent,
    DropdownMenuItem,
    DropdownMenuLabel,
    DropdownMenuSeparator,
    DropdownMenuTrigger,
} from '@/components/ui/dropdown-menu';

// Importando ícones do Lucide
import { Home, Wrench, Users, Car, Warehouse, Sun, Moon, PanelLeft } from 'lucide-vue-next';

defineProps({
    title: String,
});

// Estado para o menu mobile
const isMobileMenuOpen = ref(false);

// Lógica do Dark Mode
const isDarkMode = ref(false);
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

const logout = () => {
    router.post(route('logout'));
};

const menuItems = [
    { name: 'Dashboard', routeName: 'dashboard', icon: 'Home' },
    { name: 'Ordens de Serviço', routeName: '#', icon: 'Wrench' },
    { name: 'Clientes', routeName: '#', icon: 'Users' },
    { name: 'Veículos', routeName: '#', icon: 'Car' },
    { name: 'Estoque', routeName: '#', icon: 'Warehouse' },
];

const icons = {
    Home, Wrench, Users, Car, Warehouse
};
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
                    <div v-if="$page.props.tenant" class="text-xs text-muted-foreground">
                        {{ $page.props.tenant.name }}
                    </div>
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
                <div class="mt-auto p-4 border-t dark:border-zinc-800">
                    <div v-if="tenant" class="text-xs text-center text-muted-foreground">
                        <span>Licenciado para:</span>
                        <p class="font-semibold text-foreground">{{ tenant.name }}</p>
                    </div>
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
                <button @click="toggleDarkMode" class="p-2 rounded-full">
                    <Sun class="h-[1.2rem] w-[1.2rem] rotate-0 scale-100 transition-all dark:-rotate-90 dark:scale-0" />
                    <Moon class="absolute h-[1.2rem] w-[1.2rem] rotate-90 scale-0 transition-all dark:rotate-0 dark:scale-100" />
                </button>

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
