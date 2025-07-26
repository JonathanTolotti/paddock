<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3';
// Garantindo que os caminhos estão em minúsculo
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Building, Globe, User, Mail, Lock, LoaderCircle } from 'lucide-vue-next';

const form = useForm({
    tenant_name: '',
    user_name: '',
    email: '',
    password: '',
    password_confirmation: '',
});

const submit = () => {
    form.post(route('register'), {
        onFinish: () => form.reset('password', 'password_confirmation'),
    });
};
</script>

<template>
    <Head title="Crie sua Conta no Paddock" />
    <div class="flex flex-col items-center justify-center min-h-screen bg-muted/40 p-4">
        <div class="mx-auto w-full max-w-md">
            <div class="flex justify-center mb-8">
                <Link :href="route('dashboard')" class="text-3xl font-bold">
                    Paddock
                </Link>
            </div>

            <div class="bg-background border shadow-sm rounded-lg p-6 sm:p-8">
                <div class="grid gap-2 text-center mb-8">
                    <h1 class="text-2xl font-semibold tracking-tight">Crie sua conta</h1>
                    <p class="text-sm text-muted-foreground">
                        Preencha os dados abaixo para começar.
                    </p>
                </div>

                <form @submit.prevent="submit" class="grid gap-4">
                    <div class="grid gap-2">
                        <Label for="tenant_name">Nome da Oficina</Label>
                        <div class="relative">
                            <Building class="absolute left-3 top-1/2 -translate-y-1/2 h-4 w-4 text-muted-foreground" />
                            <Input id="tenant_name" v-model="form.tenant_name" placeholder="Ex: Super Carros" required class="pl-10" />
                        </div>
                        <p v-if="form.errors.tenant_name" class="text-sm text-destructive mt-1">{{ form.errors.tenant_name }}</p>
                    </div>

                    <hr class="my-2 border-border" />

                    <div class="grid gap-2">
                        <Label for="user_name">Seu Nome</Label>
                        <div class="relative">
                            <User class="absolute left-3 top-1/2 -translate-y-1/2 h-4 w-4 text-muted-foreground" />
                            <Input id="user_name" v-model="form.user_name" placeholder="Seu nome completo" required class="pl-10" />
                        </div>
                        <p v-if="form.errors.user_name" class="text-sm text-destructive mt-1">{{ form.errors.user_name }}</p>
                    </div>
                    <div class="grid gap-2">
                        <Label for="email">Seu E-mail</Label>
                        <div class="relative">
                            <Mail class="absolute left-3 top-1/2 -translate-y-1/2 h-4 w-4 text-muted-foreground" />
                            <Input id="email" v-model="form.email" type="email" placeholder="seu@email.com" required class="pl-10" />
                        </div>
                        <p v-if="form.errors.email" class="text-sm text-destructive mt-1">{{ form.errors.email }}</p>
                    </div>
                    <div class="grid gap-2">
                        <Label for="password">Senha</Label>
                        <div class="relative">
                            <Lock class="absolute left-3 top-1/2 -translate-y-1/2 h-4 w-4 text-muted-foreground" />
                            <Input id="password" v-model="form.password" type="password" required class="pl-10" />
                        </div>
                        <p v-if="form.errors.password" class="text-sm text-destructive mt-1">{{ form.errors.password }}</p>
                    </div>
                    <div class="grid gap-2">
                        <Label for="password_confirmation">Confirmar Senha</Label>
                        <div class="relative">
                            <Lock class="absolute left-3 top-1/2 -translate-y-1/2 h-4 w-4 text-muted-foreground" />
                            <Input id="password_confirmation" v-model="form.password_confirmation" type="password" required class="pl-10" />
                        </div>
                    </div>

                    <Button type="submit" class="w-full mt-4 h-11 text-base" :disabled="form.processing">
                        <LoaderCircle v-if="form.processing" class="h-5 w-5 animate-spin" />
                        <span v-else>Criar Conta e Acessar</span>
                    </Button>
                </form>
            </div>
            <div class="mt-6 text-center text-sm">
                Já tem uma conta?
                <Link :href="route('login')" class="font-medium text-primary underline-offset-4 hover:underline">
                    Faça login
                </Link>
            </div>
        </div>
    </div>
</template>
