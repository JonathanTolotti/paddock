<script setup>
import { cva } from 'class-variance-authority'
import { cn } from '@/lib/utils'

const props = defineProps({
    variant: {
        type: String,
        validator: (val) =>
            ['default', 'secondary', 'destructive', 'outline', 'success'].includes(val),
    },
})

const badgeVariants = cva(
    'inline-flex items-center rounded-full border px-2.5 py-0.5 text-xs font-semibold transition-colors focus:outline-none focus:ring-2 focus:ring-ring focus:ring-offset-2',
    {
        variants: {
            variant: {
                default: 'border-transparent bg-blue-600 text-primary-foreground hover:bg-blue-600/80 dark:bg-blue-700 dark:hover:bg-blue-700/80',
                secondary: 'border-transparent bg-slate-500 text-secondary-foreground hover:bg-slate-500/80 dark:bg-slate-700 dark:hover:bg-slate-700/80',
                destructive: 'border-transparent bg-destructive text-destructive-foreground hover:bg-destructive/80',
                outline: 'text-foreground',
                success: 'border-transparent bg-green-600 text-primary-foreground hover:bg-green-600/80 dark:bg-green-700 dark:hover:bg-green-700/80',
            },
        },
        defaultVariants: {
            variant: 'default',
        },
    },
)
</script>

<template>
    <div :class="cn(badgeVariants({ variant }), $attrs.class ?? '')">
        <slot />
    </div>
</template>
