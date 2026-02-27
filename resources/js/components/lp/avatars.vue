<script setup lang="ts">
const AVATARS = [
    "https://api.dicebear.com/9.x/adventurer/svg?seed=Sawyer",
    "https://api.dicebear.com/9.x/adventurer/svg?seed=George",
    "https://api.dicebear.com/9.x/adventurer/svg?seed=Jack",
    "https://api.dicebear.com/9.x/adventurer/svg?seed=Luis",
    "https://api.dicebear.com/9.x/adventurer/svg?seed=Mason",
];

const TEXTS = [
    "...",
    "...?",
    "?",
];

</script>

<template>
    <span v-for="avatar in AVATARS.length" :key="avatar">
        <UTooltip arrow defaultOpen :text="TEXTS[Math.floor(Math.random() * TEXTS.length)]" :content="{
            side: 'top'
        }" :ui="{
            content: 'surface-elevated surface-elevated-interactive font-mono'
        }">
            <UAvatar class="surface-elevated surface-elevated-interactive size-12" :src="AVATARS[avatar - 1]" />
        </UTooltip>
    </span>
</template>