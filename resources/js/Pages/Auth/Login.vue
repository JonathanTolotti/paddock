<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Checkbox } from '@/components/ui/checkbox';
import { Mail, Lock, LoaderCircle } from 'lucide-vue-next';

defineProps({
    canResetPassword: {
        type: Boolean,
    },
    status: {
        type: String,
    },
});

const form = useForm({
    email: '',
    password: '',
    remember: false,
});

const submit = () => {
    form.post(route('login'), {
        onFinish: () => form.reset('password'),
    });
};
</script>

<template>
    <Head title="Acessar Conta" />
    <div class="flex flex-col items-center justify-center min-h-screen bg-muted/40 p-4">
        <div class="mx-auto w-full max-w-md">
            <div class="flex justify-center mb-8">
                <Link :href="route('dashboard')" class="text-3xl font-bold">
                    Paddock
                </Link>
            </div>

            <div class="bg-background border shadow-sm rounded-lg p-6 sm:p-8">
                <div class="grid gap-2 text-center mb-8">
                    <h1 class="text-2xl font-semibold tracking-tight">Acesse sua conta</h1>
                    <p class="text-sm text-muted-foreground">
                        Digite seu e-mail abaixo para entrar na sua oficina.
                    </p>
                </div>

                <div v-if="status" class="mb-4 font-medium text-sm text-green-600">
                    {{ status }}
                </div>

                <form @submit.prevent="submit" class="grid gap-4">
                    <div class="grid gap-2">
                        <Label for="email">E-mail</Label>
                        <div class="relative">
                            <Mail class="absolute left-3 top-1/2 -translate-y-1/2 h-4 w-4 text-muted-foreground" />
                            <Input
                                id="email"
                                v-model="form.email"
                                type="email"
                                placeholder="seu@email.com"
                                required
                                autofocus
                                class="pl-10"
                            />
                        </div>
                        <p v-if="form.errors.email" class="text-sm text-destructive mt-1">{{ form.errors.email }}</p>
                    </div>

                    <div class="grid gap-2">
                        <div class="flex items-center">
                            <Label for="password">Senha</Label>
                            <Link v-if="canResetPassword" :href="route('password.request')" class="ml-auto inline-block text-sm underline">
                                Esqueceu a senha?
                            </Link>
                        </div>
                        <div class="relative">
                            <Lock class="absolute left-3 top-1/2 -translate-y-1/2 h-4 w-4 text-muted-foreground" />
                            <Input
                                id="password"
                                v-model="form.password"
                                type="password"
                                required
                                class="pl-10"
                            />
                        </div>
                        <p v-if="form.errors.password" class="text-sm text-destructive mt-1">{{ form.errors.password }}</p>
                    </div>

                    <div class="flex items-center space-x-2">
                        <Checkbox id="remember" v-model:checked="form.remember" />
                        <label
                            for="remember"
                            class="text-sm font-medium leading-none peer-disabled:cursor-not-allowed peer-disabled:opacity-70"
                        >
                            Lembrar de mim
                        </label>
                    </div>

                    <Button type="submit" class="w-full mt-2 h-11 text-base" :disabled="form.processing">
                        <LoaderCircle v-if="form.processing" class="h-5 w-5 animate-spin" />
                        <span v-else>Entrar</span>
                    </Button>
                </form>
            </div>
        </div>
    </div>
</template>
