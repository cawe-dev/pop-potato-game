<script setup lang="ts">
defineProps<{
    icon: string;
    theme: string;
    users: number;
    maxUsers: number;
    type: string;
}>();
</script>

<template>
    <div class="ticket-wrapper cursor-pointer min-w-xs">
        <div class="ticket-shape bg-card text-card-foreground p-3 flex items-center justify-between">
            <div class="flex items-center gap-4 pl-4 sm:pl-6">
                <UIcon :name="icon" class="w-10 h-10 text-primary" />
                <span class="font-black uppercase text-lg leading-tight">{{ theme }}</span>
            </div>
            <div class="flex flex-col items-end pr-4 sm:pr-6">
                <span class="font-bold text-xl">{{ users }}/{{ maxUsers }}</span>
                <span v-if="type" class="font-bold text-xs uppercase text-muted-foreground tracking-widest mt-1">
                    {{ type }}
                </span>
            </div>
        </div>
    </div>
</template>

<style scoped>
</style>