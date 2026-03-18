<script setup lang="ts">
import { ref, computed } from 'vue';
import IconButton from '../brutal-ui/IconButton.vue';

type DrawerDirection = 'right' | 'bottom';

export interface ThemeOption {
    value: string;
    label: string;
    icon?: string;
}

const isOpen = defineModel<boolean>('open');

const props = defineProps<{
    direction: DrawerDirection;
    themes: ThemeOption[];
}>();

const form = ref({
    theme: '',
    max_users: 4,
    password: ''
});

const incrementUsers = () => {
    if (form.value.max_users < 20) form.value.max_users++;
};

const decrementUsers = () => {
    if (form.value.max_users > 2) form.value.max_users--;
};

const submit = () => {
};

</script>

<template>
    <UDrawer v-model:open="isOpen" :direction="direction">
        <slot />

        <template #body>
            <div class="flex flex-col h-full gap-6 px-4 py-6">

                <h2 class="font-black text-3xl uppercase tracking-widest text-foreground brutal-border-b pb-4 mb-2">
                    New Room
                </h2>

                <div class="flex-1 flex flex-col gap-8">

                    <div class="space-y-2">
                        <label class="block font-black uppercase text-sm tracking-widest text-foreground">Theme</label>

                        <USelect v-model="form.theme" :items="themes" placeholder="Selecione um jogo" variant="none"
                            class="brutal-input uppercase" :ui="{
                                trailingIcon: 'text-foreground size-6',
                                content: 'brutal-border shadow-[4px_4px_0px_var(--brutal-edge)] rounded-xl'
                            }" />
                    </div>

                    <div class="space-y-2">
                        <label
                            class="block font-black uppercase text-sm tracking-widest text-foreground">Players</label>
                        <div class="flex items-center gap-4">
                            <IconButton icon="i-lucide-minus" @click="decrementUsers" />

                            <span class="text-4xl font-black flex-1 text-center tabular-nums">
                                {{ form.max_users }}
                            </span>

                            <IconButton icon="i-lucide-plus" @click="incrementUsers" />
                        </div>
                    </div>

                    <div class="space-y-2">
                        <label class="block font-black uppercase text-sm tracking-widest text-foreground">
                            Password <span class="text-muted-foreground text-xs font-bold">(Optional)</span>
                        </label>

                        <UInput type="password" v-model="form.password" placeholder="Leave blank for public"
                            variant="none" class="brutal-input font-bold" />
                    </div>

                </div>

                <div class="mt-auto pt-8">
                    <button @click="submit"
                        class="w-full bg-primary text-primary-foreground p-4 rounded-xl font-black text-2xl uppercase tracking-widest surface-elevated surface-elevated-interactive hover:bg-primary/90 focus:outline-none focus-visible:ring-4 focus-visible:ring-ring focus-visible:ring-offset-2 focus-visible:ring-offset-background">
                        Create Room!
                    </button>
                </div>

            </div>
        </template>
    </UDrawer>
</template>