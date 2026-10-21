<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import { useEcho } from '@laravel/echo-vue';
import PlaceholderPattern from '@/components/PlaceholderPattern.vue';
import { dashboard } from '@/routes';
import { store, show, join } from '@/routes/games';
import { ref } from 'vue';


defineOptions({
    layout: {
        breadcrumbs: [
            {
                title: 'Dashboard',
                href: dashboard(),
            },
        ],
    },
});

const props = defineProps<{
    games: {
        data: Array<{
            id: number;
            player_one_id: number;
            player_two_id: number | null;
            player_one: {
                name: string;
            };
            player_two: {
                name: string;
            } | null;
        }>;
        links: Array<{
            url: string | null;
            label: string;
            active: boolean;
        }>;
    };
}>();

const games = ref(props.games.data);

useEcho<{ game: { id: number } }>('lobby', 'GameJoined', (event) => {
    games.value = games.value.filter((game) => game.id !== event.game.id);
});

</script>

<template>
    <Head title="Dashboard" />

    <div
        class="flex h-full flex-1 flex-col gap-4 overflow-x-auto rounded-xl p-4"
    >
        <div class="grid auto-rows-min gap-4 md:grid-cols-3">
            <div
                class="relative aspect-video overflow-hidden rounded-xl border border-sidebar-border/70 dark:border-sidebar-border"
            >
                <ul>
                    <li
                        v-for="game in games"
                        :key="game.id"
                        class="flex items-center justify-between gap-2 border-b border-sidebar-border/70 px-4 py-2 last:border-b-0 dark:border-sidebar-border"
                    >
                        <Link
                            :href="show(game.id)"
                            class="text-sm font-medium text-sidebar-text/70"
                        >
                            {{ game.player_one.name }} vs {{ game.player_two ? game.player_two.name : 'Waiting for player' }}
                        </Link>
                        <span
                            class="rounded-full bg-accent px-2 py-1 text-xs font-medium text-accent-foreground"
                        >
                            <Link
                                v-if="!game.player_two"
                                :href="join(game.id)"
                                method="post"
                                class="text-sm font-medium text-sidebar-text/70"
                            >
                                Join Game
                            </Link>
                        </span>
                    </li>
                </ul>
            </div>
            <div
                class="relative aspect-video overflow-hidden rounded-xl border border-sidebar-border/70 dark:border-sidebar-border"
            >
                <PlaceholderPattern />
            </div>
            <div
                class="relative aspect-video overflow-hidden rounded-xl border border-sidebar-border/70 dark:border-sidebar-border"
            >
                <PlaceholderPattern />
            </div>
        </div>
        <div
            class="relative min-h-[100vh] flex-1 rounded-xl border border-sidebar-border/70 md:min-h-min dark:border-sidebar-border"
        >
            <Link
                :href="store()"
                method="post"
                class="absolute inset-0 flex items-center justify-center text-sm font-medium text-sidebar-text/70"
            >
                Create a new game
            </Link>
        </div>
    </div>
</template>
