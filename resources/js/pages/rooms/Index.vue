<script setup lang="ts">
import { Head } from '@inertiajs/vue3';
import TicketRoom from '@/components/Room/TicketRoom.vue';
import TicketCreateRoom from '@/components/Room/TicketCreateRoom.vue';
import AppLayout from '@/layouts/AppLayout.vue';
import { computed, ref } from 'vue';

import { useMediaQuery } from '@vueuse/core';

import type { PaginatedData } from '@/types/pagination';
import type { Room } from '@/types/models/room';
import DrawerCreateRoom from '@/components/Room/DrawerCreateRoom.vue';

const props = defineProps<{
    paginatedRooms: PaginatedData<Room>;
    themes: [];
}>();

const rooms = computed(() => {
    return props.paginatedRooms.data;
});

const isDesktop = useMediaQuery('(min-width: 768px)');
const isDrawerOpen = ref(false);
</script>

<template>
    <Head title="Home Page" />

    <AppLayout class="min-h-screen">
        <UContainer class="min-h-full py-8">
            <UPageGrid>
                <DrawerCreateRoom
                    v-model:open="isDrawerOpen"
                    :direction="isDesktop ? 'right' : 'bottom'"
                    :themes="themes"
                >
                    <TicketCreateRoom />
                </DrawerCreateRoom>

                <TicketRoom
                    v-for="room in rooms"
                    :key="room.id"
                    :icon="room.icon"
                    :theme="room.theme"
                    :users="room.users_count"
                    :max-users="room.max_users"
                    :type="room.type"
                />
            </UPageGrid>
        </UContainer>
    </AppLayout>
</template>
