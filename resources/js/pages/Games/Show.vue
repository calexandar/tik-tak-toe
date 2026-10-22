<script setup lang="ts">
import AuthSimpleLayout from '@/layouts/auth/AuthSimpleLayout.vue';
import { ref, computed } from 'vue';

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

const boardState = ref<[0,0,0,0,0,0,0,0,0]>([0, 0, 0, 0, 0, 0, 0, 0, 0]);

const xTurn = computed(() => {
   return boardState.value.reduce((carry, value) => carry + value, 0) === 0;
});

const fillSquare = (index: number) => {
        boardState.value[index] = xTurn.value ? -1 : 1; // Example: Fill with player one's mark

};

</script>

<template>
    <AuthSimpleLayout>
        <menu class="grid grid-cols-3 gap-1.5 w-0 min-w-fit">
            <li v-for="(square, index) in boardState" class="bg-gray-300 text-black size-32 grid place-items-center">
                <button @click="fillSquare(index)" v-if="square === 0" class="place-self-stretch bg-gray-200 hover:bg-gray-300 transition-colors"></button>
                <span v-else v-text="square === -1 ? 'X' : 'O'" class="text-4xl font-bold"></span>
            </li>
        </menu>
    </AuthSimpleLayout>
</template>