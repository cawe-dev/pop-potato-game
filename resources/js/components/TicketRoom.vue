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
.ticket-wrapper {
    filter:
        drop-shadow(3px 0px 0px #1A1A1A) drop-shadow(-3px 0px 0px #1A1A1A) drop-shadow(0px 3px 0px #1A1A1A) drop-shadow(0px -3px 0px #1A1A1A) drop-shadow(4px 4px 0px #1A1A1A);
    transition: filter 0.15s ease, transform 0.15s ease;
}

.ticket-shape {
    border-radius: var(--radius-lg, 0.75rem);

    mask-image:
        radial-gradient(circle 14px at 0% 50%, transparent 14px, black 14.5px),
        radial-gradient(circle 14px at 100% 50%, transparent 14px, black 14.5px);

    mask-size: 51% 100%;
    mask-position: left, right;
    mask-repeat: no-repeat;
}

.ticket-wrapper:hover {
    filter:
        drop-shadow(3px 0px 0px #1A1A1A) drop-shadow(-3px 0px 0px #1A1A1A) drop-shadow(0px 3px 0px #1A1A1A) drop-shadow(0px -3px 0px #1A1A1A) drop-shadow(6px 6px 0px #1A1A1A);
    transform: translate(-2px, -2px);
}

.ticket-wrapper:active {
    filter:
        drop-shadow(3px 0px 0px #1A1A1A) drop-shadow(-3px 0px 0px #1A1A1A) drop-shadow(0px 3px 0px #1A1A1A) drop-shadow(0px -3px 0px #1A1A1A) drop-shadow(1px 1px 0px #1A1A1A);
    transform: translate(2px, 2px);
}
</style>