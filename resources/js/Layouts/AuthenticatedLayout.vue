<script setup>
import { ref, onMounted, watch } from 'vue';
import { Head, Link, router } from '@inertiajs/vue3';

defineProps({
    title: String,
});

// --- Estado para o menu mobile ---
const isMobileMenuOpen = ref(false);

const showingNavigationDropdown = ref(false);
const isDarkMode = ref(false);

const toggleDarkMode = () => {
    isDarkMode.value = !isDarkMode.value;
};

watch(isDarkMode, (newValue) => {
    if (newValue) {
        document.documentElement.classList.add('dark');
        localStorage.setItem('darkMode', 'enabled');
    } else {
        document.documentElement.classList.remove('dark');
        localStorage.setItem('darkMode', 'disabled');
    }
});

onMounted(() => {
    if (localStorage.getItem('darkMode') === 'enabled' ||
        (window.matchMedia('(prefers-color-scheme: dark)').matches && localStorage.getItem('darkMode') !== 'disabled')) {
        isDarkMode.value = true;
    }
});

const logout = () => {
    router.post(route('logout'));
};
</script>

<template>
    <Head :title="title" />

    <div class="flex h-screen bg-gray-100 dark:bg-gray-900">
        <div v-show="isMobileMenuOpen" class="fixed inset-0 z-20 bg-black bg-opacity-50 lg:hidden" @click="isMobileMenuOpen = false"></div>

        <aside
            class="w-64 flex-shrink-0 bg-gray-800 text-gray-200 p-4 transform transition-transform duration-300 ease-in-out -translate-x-full lg:translate-x-0 lg:static fixed inset-y-0 left-0 z-30"
            :class="{ 'translate-x-0': isMobileMenuOpen }"
        >
            <div class="mb-10 text-center">
                <Link :href="route('dashboard')" class="text-2xl font-bold text-white">
                    Paddock
                </Link>
            </div>
            <nav class="flex flex-col space-y-2">
                <Link :href="route('dashboard')"
                      :class="{ 'bg-gray-700 text-white': route().current('dashboard') }"
                      class="px-4 py-2 rounded-md hover:bg-gray-700 hover:text-white transition-colors">
                    Dashboard
                </Link>
                <Link href="#" class="px-4 py-2 rounded-md hover:bg-gray-700 hover:text-white transition-colors">Clientes</Link>
                <Link href="#" class="px-4 py-2 rounded-md hover:bg-gray-700 hover:text-white transition-colors">Veículos</Link>
                <Link href="#" class="px-4 py-2 rounded-md hover:bg-gray-700 hover:text-white transition-colors">Ordens de Serviço</Link>
                <Link href="#" class="px-4 py-2 rounded-md hover:bg-gray-700 hover:text-white transition-colors">Estoque de Peças</Link>
            </nav>
        </aside>

        <div class="flex-1 flex flex-col overflow-hidden">
            <header class="bg-white dark:bg-gray-800 border-b border-gray-200 dark:border-gray-700">
                <div class="w-full mx-auto py-4 px-4 sm:px-6 lg:px-8 flex justify-between items-center">
                    <button @click="isMobileMenuOpen = !isMobileMenuOpen" class="text-gray-500 dark:text-gray-400 focus:outline-none lg:hidden">
                        <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M4 6h16M4 12h16M4 18h16" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"></path>
                        </svg>
                    </button>

                    <div class="hidden lg:block"></div>

                    <div class="flex items-center">
                        <button @click="toggleDarkMode" class="mr-4 p-2 rounded-full text-gray-500 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700 focus:outline-none">
                            <svg v-if="!isDarkMode" class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
                            <svg v-else class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"></path></svg>
                        </button>

                        <div class="relative">
                            <button @click="showingNavigationDropdown = !showingNavigationDropdown" class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-500 dark:text-gray-400 bg-white dark:bg-gray-800 hover:text-gray-700 dark:hover:text-gray-300 focus:outline-none transition ease-in-out duration-150">
                                {{ $page.props.auth.user.name }}
                                <svg class="ms-2 -me-0.5 h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" /></svg>
                            </button>
                            <div v-show="showingNavigationDropdown" class="absolute z-50 mt-2 w-48 rounded-md shadow-lg origin-top-right right-0" @click.away="showingNavigationDropdown = false">
                                <div class="rounded-md ring-1 ring-black ring-opacity-5 py-1 bg-white dark:bg-gray-700">
                                    <Link :href="route('profile.edit')" class="block w-full px-4 py-2 text-start text-sm leading-5 text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-600 focus:outline-none focus:bg-gray-100 dark:focus:bg-gray-600 transition duration-150 ease-in-out">Perfil</Link>
                                    <button @click="logout" type="button" class="block w-full px-4 py-2 text-start text-sm leading-5 text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-600 focus:outline-none focus:bg-gray-100 dark:focus:bg-gray-600 transition duration-150 ease-in-out">
                                        Sair
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </header>

            <main class="flex-1 overflow-x-hidden overflow-y-auto">
                <div class="container mx-auto px-6 py-8">
                    <header v-if="$slots.header" class="mb-6">
                        <slot name="header" />
                    </header>
                    <slot />
                </div>
            </main>
        </div>
    </div>
</template>
