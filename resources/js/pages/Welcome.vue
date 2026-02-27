<script setup lang="ts">
import { Head } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import { onMounted, onUnmounted, ref, watch } from 'vue';
import Avatars from '@/components/lp/Avatars.vue';
import { useTyping } from '@/composables/useTyping';
import { THEMES } from '@/constants/themes';
import { QUESTIONS } from '@/constants/questions';
import PixelEmojis from '@/components/lp/PixelEmojis.vue';

const currentThemeIndex = ref(0);
const { typedText: question, typeText } = useTyping();

let timer: NodeJS.Timeout | null = null;

watch(currentThemeIndex, (newIndex) => {
    typeText(QUESTIONS[newIndex]);
}, { immediate: true });

onMounted(() => {
    timer = setInterval(() => {
        currentThemeIndex.value = (currentThemeIndex.value + 1) % THEMES.length;
    }, 4000);
});

onUnmounted(() => {
    if (timer) clearInterval(timer);
});
</script>

<template>

    <Head title="Welcome">
        <link rel="preconnect" href="https://rsms.me/" />
        <link rel="stylesheet" href="https://rsms.me/inter/inter.css" />
    </Head>

    <AppLayout>
        <PixelEmojis />
        <UPage>
            <template #left>
                <UPageAside>

                </UPageAside>
            </template>

            <UPageHero :ui="{
                headline: 'text-foreground border-4 surface-elevated surface-elevated-interactive px-4 py-2 md:px-8 md:py-3 text-center',
                title: 'font-light flex justify-center gap-5 mt-12',
                description: 'text-foreground font-mono leading-relaxed text-balance text-3xl sm:text-4xl font-bold'
            }">
                <template #headline>
                    <span>
                        TEMA:
                    </span>
                    <span class="font-bold">
                        {{ THEMES[currentThemeIndex] }}
                    </span>
                </template>

                <template #title>
                    <Avatars />
                </template>

                <template #description>
                    <span class="border-r-4 border-foreground pr-1 animate-cursor">
                        {{ question }}
                    </span>
                </template>

                <template #links>
                    <UContainer>
                        <UButton
                            class="surface-elevated surface-elevated-interactive bg-accent hover:bg-accent/85 active:bg-accent/85 text-5xl py-10"
                            block label="JOGAR" />
                    </UContainer>
                </template>
            </UPageHero>

            <template #right>
                <UPageAside>
                </UPageAside>
            </template>
        </UPage>
    </AppLayout>
</template>