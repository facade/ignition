<template>
    <div>
        <div class="layout-col z-10">
            <DangerCard v-if="this.appEnv !== 'local' && this.appDebug === true">
                <p>
                    <code>APP_DEBUG</code> is set to <code>true</code> while <code>APP_ENV</code> is
                    not <code>local</code>
                </p>
                <p class="text-base">
                    This could make your application vulnerable to remote execution.
                    <a
                        class="underline"
                        target="_blank"
                        rel="noopener"
                        href="https://flareapp.io/docs/ignition-for-laravel/security"
                        >Read more about Ignition security.</a
                    >
                </p>
            </DangerCard>
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
import DangerCard from './Shared/DangerCard';
import ErrorCard from './Shared/ErrorCard';
import SolutionCard from './Solutions/SolutionCard';

export default {
    components: {
        DangerCard,
        SolutionCard,
        ErrorCard,
        FilePath,
    },

    inject: ['report', 'solutions', 'appEnv', 'appDebug'],

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
