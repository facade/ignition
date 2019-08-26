<template>
    <div>
        <div class="layout-col z-10">
            <ErrorCard />
        </div>

        <div class="layout-col z-1" v-if="solutions.length > 0">
            <SolutionCard v-bind="{ solution }" />

            <div
                class="absolute left-0 bottom-0 w-full h-8 mb-2 px-4 text-sm z-10"
                v-if="solutions.length > 1"
            >
                <ul class="grid cols-auto place-center gap-1">
                    <li
                        v-for="(solution, key) in solutions"
                        @click="activeSolutionKey = key"
                        :key="solution.class"
                    >
                        <a
                            class="grid place-center h-8 min-w-8 px-2 rounded-full"
                            :class="{
                                'bg-tint-200 font-semibold': activeSolutionKey === key,
                                'hover:bg-tint-100 cursor-pointer': activeSolutionKey !== key,
                            }"
                        >
                            {{ key + 1 }}
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</template>

<script>
import FilePath from './Shared/FilePath.vue';
import ErrorCard from './Shared/ErrorCard';
import SolutionCard from './Solutions/SolutionCard';

export default {
    components: {
        SolutionCard,
        ErrorCard,
        FilePath,
    },

    inject: ['report', 'solutions'],

    data() {
        return {
            activeSolutionKey: 0,
        };
    },

    computed: {
        firstFrame() {
            return this.report.stacktrace[0];
        },

        solution() {
            return this.solutions[this.activeSolutionKey];
        },
    },
};
</script>
