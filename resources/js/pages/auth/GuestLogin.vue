<script setup lang="ts">
import { useForm, Head } from '@inertiajs/vue3';
import type { FormSubmitEvent, AuthFormField } from '@nuxt/ui';
import * as z from 'zod';
import AppLayout from '@/layouts/AppLayout.vue';
import { store } from '@/routes/guest-login';

const fields: AuthFormField[] = [
    {
        name: 'nickname',
        type: 'text',
        label: 'Nickname',
        placeholder: 'Enter your nickname',
        required: true,
        ui: { input: 'surface-elevated-sm focus:ring-primary' }
    },
];

const schema = z.object({
    nickname: z.string().min(3, 'Nickname must be at least 3 characters'),
});

type Schema = z.output<typeof schema>;

const form = useForm({
    nickname: '',
});

function onLogin(event: FormSubmitEvent<Schema>) {
    form.nickname = event.data.nickname;

    form.submit(store(), {
        onSuccess: () => {
            form.reset();
        },
    });
}
</script>

<template>

    <Head title="Guest Log in" />

    <AppLayout>
        <div class="flex flex-col items-center justify-center min-h-screen gap-4 p-4 font-sans bg-background">

            <UPageCard class="w-full max-w-md surface-elevated bg-card">

                <UAuthForm title="Guest Login" description="Enter your nickname to join the session."
                    icon="i-lucide-user" :fields="fields" :schema="schema" :loading="form.processing" @submit="onLogin"
                    :submit="{
                        label: 'Join Session',
                        color: 'primary',
                        class: 'surface-elevated-sm surface-elevated-interactive w-full font-bold'
                    }" :ui="{
                        title: 'text-2xl text-primary',
                        description: 'text-text-muted',
                        icon: 'text-primary'
                    }">
                    <template #validation v-if="form.errors.nickname">
                        <UAlert color="danger" variant="subtle" icon="i-lucide-alert-circle"
                            :title="form.errors.nickname" class="surface-elevated-sm border-danger text-danger" />
                    </template>
                </UAuthForm>

            </UPageCard>
        </div>
    </AppLayout>
</template>