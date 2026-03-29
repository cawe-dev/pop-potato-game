<script setup lang="ts">
import { ref, onMounted, onUnmounted } from 'vue';

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

const activeTexts = ref<string[]>(
    AVATARS.map(() => TEXTS[Math.floor(Math.random() * TEXTS.length)])
);

const updateRandomTexts = () => {
    activeTexts.value = activeTexts.value.map(() =>
        TEXTS[Math.floor(Math.random() * TEXTS.length)]
    );
};

let randomTextInterval: ReturnType<typeof setInterval>;

onMounted(() => {
    randomTextInterval = setInterval(updateRandomTexts, 3000);
});

onUnmounted(() => {
    clearInterval(randomTextInterval);
});
</script>

<template>
    <span v-for="avatar in AVATARS.length" :key="avatar" class="animate-bounce-soft" :style="{
        animationDuration: `${2 + avatar * 0.2}s`,
        animationDelay: `${avatar * 0.1}s`
    }">
        <UTooltip arrow defaultOpen open :text="activeTexts[avatar - 1]" :content="{
            side: 'top'
        }" :ui="{
            content: 'surface-elevated surface-elevated-interactive font-mono'
        }">
            <UAvatar class="surface-elevated surface-elevated-interactive size-12" :src="AVATARS[avatar - 1]" />
        </UTooltip>
    </span>
</template>