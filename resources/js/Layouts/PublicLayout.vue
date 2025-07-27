<script setup>
import { Head, Link } from '@inertiajs/vue3';
</script>
<template>
    <Head title="Orçamento" />
    <div class="min-h-screen bg-gray-100 dark:bg-zinc-900 text-gray-900 dark:text-gray-100">
        <header class="py-6">
            <div class="container mx-auto text-center">
                <Link href="/" class="text-3xl font-bold">
                    Paddock
                </Link>
            </div>
        </header>
        <main>
            <slot />
        </main>
        <footer class="py-6 text-center text-sm text-muted-foreground mt-12">
            © {{ new Date().getFullYear() }} Paddock. Todos os direitos reservados.
        </footer>
    </div>
</template>
