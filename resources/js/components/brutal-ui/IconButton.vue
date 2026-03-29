<script setup lang="ts">
const emit = defineEmits(['click'])
defineProps<{
    icon: string;
}>();
</script>

<template>
    <UButton @click="emit('click')"
        class="bg-card text-card-foreground w-14 h-14 flex items-center justify-center rounded-xl text-3xl font-black surface-elevated surface-elevated-interactive focus:outline-none focus-visible:ring-4 focus-visible:ring-ring">
        <UIcon :name="icon" class="w-6 h-6" />
    </UButton>
</template>