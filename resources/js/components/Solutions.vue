<template>
    <section class="mb-8 relative" v-if="solutions.length">
        <div class="px-8 pt-8 pb-4 bg-primary-300 border-b border-gray-400">
            <div
                class="absolute flex items-center rounded-full h-8 px-4 text-sm z-10"
                style="left:50%;bottom:.5rem"
                v-if="solutions.length > 1"
            >
                <ul class="text-gray-500 grid cols-auto justify-center gap-2">
                    <li
                        v-for="(solution, key) in solutions"
                        @click="activeSolutionKey = key"
                        :key="solution.class"
                        class="rounded-full bg-primary-500 text-200 flex justify-center items-center w-8 h-8 mr-2"
                        :class="{ 'font-semibold': activeSolutionKey === key }"
                    >
                        {{ key + 1 }}
                    </li>
                </ul>
            </div>
            <a href="#" class="absolute top-0 mr-2 right-0 mt-2">×</a>
            <Solution v-bind="{ solution }" />
            <div class="text-gray-600 mt-8 px-8 text-sm mt-2 | sm:flex items-start justify-end">
                <div><a class="underline" href="#">Improve suggestions</a></div>
            </div>
        </div>
    </section>
</template>

<script>
import Solution from './Solutions/Solution';

export default {
    components: { Solution },

    inject: ['solutions'],

    data() {
        return {
            activeSolutionKey: 0,
        };
    },

    computed: {
        solution() {
            return this.solutions[this.activeSolutionKey];
        },
    },
};
</script>
