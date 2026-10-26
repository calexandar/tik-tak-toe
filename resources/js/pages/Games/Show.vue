<script setup lang="ts">
import AuthSimpleLayout from '@/layouts/auth/AuthSimpleLayout.vue';
import { ref, computed } from 'vue';
import { useEchoPresence } from '@laravel/echo-vue';
import { router, usePage } from '@inertiajs/vue3';
import { update } from '@/routes/games';

const props =defineProps<{
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
        state: number[] | null;
    };
}>();

const page = usePage();

const boardState = ref<number[] | null>(props.game.state || [0, 0, 0, 0, 0, 0, 0, 0, 0]);
const players = ref<User[]>([]);

const xTurn = computed(() => {
   return boardState.value.reduce((carry, value) => carry + value, 0) === 0;
});
const yourTurn = computed(() => {

    if (props.game.player_one_id === page.props.auth.user.id) {
        return xTurn.value;
    }

    return !xTurn.value;
});

const lines = [
    [0, 1, 2],
    [3, 4, 5],
    [6, 7, 8],
    [0, 3, 6],
    [1, 4, 7],
    [2, 5, 8],      
    [0, 4, 8],
    [2, 4, 6]
];

const fillSquare = (index: number) => {
        if (!yourTurn.value) {
            return;
        }

        boardState.value[index] = xTurn.value ? -1 : 1; // Example: Fill with player one's mark

        router.put(update(props.game.id), {
            state: boardState.value
        });

        checkForWinner();
};

const checkForWinner = () => {
       const winingLines = lines.map((line) => line.reduce((carry, index) => carry + boardState.value[index], 0))
            .find((sum) => Math.abs(sum) === 3);
            
            if (winingLines === -3) {
                alert('Player One Wins!');
                return;
            } 
            
            if (winingLines === 3) {
                alert('Player Two Wins!');
                return;
            }

            if (! boardState.value.includes(0)) {
                alert('Draw!');
                return;
            }

};

const resetGame = () => {
    boardState.value = [0, 0, 0, 0, 0, 0, 0, 0, 0];

    router.put(update(props.game.id), {
        state: boardState.value
    });
   
};
interface User {
  id: number;
  name: string;
  // add other user properties here
}

const channel = useEchoPresence(`game.${props.game.id}`).channel();

channel.here((users: User[]) => {
    players.value = users;
});

channel.joining((user: User) => {
    router.reload({
        onSuccess: () => players.value.push(user)
    });
});

channel.leaving((user: User) => {
    players.value = players.value.filter((u) => u.id !== user.id);
});

channel.listen('PlayerMadeMove', (event: { game: { state: number[] } }) => {
    boardState.value = event.game.state;
    checkForWinner();
});

</script>

<template>
    <AuthSimpleLayout>
        <menu class="grid grid-cols-3 gap-1.5 w-0 min-w-fit">
            <li v-for="(square, index) in boardState" class="bg-gray-300 text-black size-32 grid place-items-center">
                <button @click="fillSquare(index)" v-if="square === 0" class="place-self-stretch bg-gray-200 hover:bg-gray-300 transition-colors"></button>
                <span v-else v-text="square === -1 ? 'X' : 'O'" class="text-4xl font-bold"></span>
            </li>
        </menu>

        <ul>
            <li class="flex gap-2 items-center space-y-2">
                <span class="p-1.5 bg-gray-200 font-bold rounded text-black" :class="{'bg-green-500' : xTurn}">X</span>
                <span>{{ game.player_one.name }}</span>
                <span :class="{'!bg-green-500' : players.find(({id}) => id === game.player_one_id)}" class="bg-red-500 size-2 rounded-full"></span>
            </li>
            <li v-if="game.player_two" class="flex gap-2 items-center">
                <span class="p-1.5 bg-gray-200 font-bold rounded text-black" :class="{'bg-green-500' : !xTurn}">O</span>
                <span>{{ game.player_two.name }}</span>
                <span :class="{'!bg-green-500' : players.find(({id}) => id === game.player_two_id)}" class="bg-red-500 size-2 rounded-full"></span>
            </li>
            <li v-else class="flex gap-2 items-center">
                <span class="p-1.5 bg-gray-200 font-bold rounded text-black">O</span>
                <span>Waiting for player two...</span>
                <span class="bg-gray-500 size-2 rounded-full"></span>
            </li>
        </ul>
    </AuthSimpleLayout>
</template>