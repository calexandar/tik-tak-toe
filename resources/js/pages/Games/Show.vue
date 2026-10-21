<script setup lang="ts">
import AuthSimpleLayout from '@/layouts/auth/AuthSimpleLayout.vue';

defineProps<{
    game: {
        id: number;
        player_one_id: number;
        player_two_id: number | null;
        player_one: {
            name: string;
        };
        player_two: {
            name: string;
        } | null;
    };
}>();

</script>

<template>
    <AuthSimpleLayout>
        <template #header>
            <h1 class="text-2xl font-semibold text-sidebar-text">
                Game
            </h1>
        </template>

        <template #default>
            <div class="flex h-full flex-1 flex-col gap-4 overflow-x-auto rounded-xl p-4">
<h1>{{ game }}</h1>
            </div>
        </template>
    </AuthSimpleLayout>
</template>