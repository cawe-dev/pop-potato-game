import { ref, onUnmounted } from 'vue';

export function useTyping() {
    const typedText = ref("");
    let typingTimer: NodeJS.Timeout | null = null;

    const typeText = (text: string) => {
        typedText.value = "";
        let i = 0;

        if (typingTimer) clearInterval(typingTimer);

        typingTimer = setInterval(() => {
            if (i < text.length) {
                typedText.value += text.charAt(i);
                i++;
            } else {
                if (typingTimer) clearInterval(typingTimer);
            }
        }, 40);
    };

    onUnmounted(() => {
        if (typingTimer) clearInterval(typingTimer);
    });

    return {
        typedText,
        typeText
    };
}