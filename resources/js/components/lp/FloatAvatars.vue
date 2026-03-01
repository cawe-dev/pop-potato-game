<script setup lang="ts">
import { FloatAvatar } from '@/types/floatAvatars';
import { AVATAR_TYPE } from '@/constants/avatars';

const columns = 4;
const rows = 5;

const floatAvatars: FloatAvatar[] = Array.from({ length: columns * rows }, (_, i) => {
    const col = i % columns;
    const row = Math.floor(i / columns);

    const sectionWidth = 100 / columns;
    const sectionHeight = 100 / rows;

    const jitterX = (Math.random() - 0.5) * sectionWidth * 0.7;
    const jitterY = (Math.random() - 0.5) * sectionHeight * 0.7;

    return {
        id: i,
        localeX: (col * sectionWidth) + (sectionWidth / 2) + jitterX,
        localeY: (row * sectionHeight) + (sectionHeight / 2) + jitterY,
        src: `https://api.dicebear.com/9.x/${AVATAR_TYPE[i % AVATAR_TYPE.length]}/svg?seed=${Math.random()}`,
        speed: 3 + Math.random() * 4,
        delay: Math.random() * -5,
        scale: 0.6 + Math.random() * 0.4,
    };
});
</script>

<template>
    <div class="fixed inset-0 overflow-hidden pointer-events-none">
        <UTooltip v-for="floatAvatar in floatAvatars" :key="floatAvatar.id" arrow defaultOpen open text="..." :content="{
            side: 'top'
        }" :ui="{
            content: 'border border-foreground font-mono opacity-10'
        }">
            <UAvatar :src="floatAvatar.src" class="absolute animate-bounce-soft opacity-10" :style="{
                left: `${floatAvatar.localeX}%`,
                top: `${floatAvatar.localeY}%`,
                width: '40px',
                height: '40px',
                transform: `scale(${floatAvatar.scale})`,
                animationDuration: `${floatAvatar.speed}s`,
                animationDelay: `${floatAvatar.delay}s`
            }" />
        </UTooltip>
    </div>
</template>