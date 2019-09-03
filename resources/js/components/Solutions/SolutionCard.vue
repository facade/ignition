<template>
    <div>
        <div
            class="solution-toggle"
            @click="toggleSolutions"
            :class="{
                'solution-toggle-show': isHidingSolutions,
            }"
        >
            <a v-if="isHidingSolutions" class="link-solution" target="_blank"
                ><i class="far fa-lightbulb text-xs mr-1"></i> Show solutions</a
            >
            <a v-else class="link-solution" target="_blank">Hide solutions</a>
        </div>
        <div
            ref="solutionCard"
            class="solution"
            :class="{
                'solution-hidden': isHidingSolutions,
            }"
        >
            <div class="solution-main">
                <div class="solution-background mx-0">
                    <svg
                        class="hidden absolute right-0 h-full | md:block"
                        x="0px"
                        y="0px"
                        viewBox="0 0 299 452"
                    >
                        <g style="opacity: 0.075">
                            <polygon
                                style="fill:rgb(63,63,63)"
                                points="298.1,451.9 150.9,451.9 21,226.9 298.1,227.1"
                            />
                            <polygon
                                style="fill:rgb(151,151,151)"
                                points="298.1,227.1 21,226.9 150.9,1.9 298.1,1.9"
                            />
                        </g>
                    </svg>
                </div>
                <div class="p-12">
                    <div class="solution-content ml-0">
                        <h2 class="solution-title">{{ solution.title }}</h2>

                        <div
                            v-if="solution.description"
                            v-html="markdown(solution.description)"
                        ></div>

                        <div v-if="solution.is_runnable">
                            <p v-html="markdown(solution.action_description)"></p>
                            <p v-if="canExecuteSolutions === null" class="py-4 text-sm italic">
                                Loading...
                            </p>
                            <div class="mt-4">
                                <button
                                    v-if="
                                        solution.is_runnable &&
                                            canExecuteSolutions === true &&
                                            executionSuccessful === null
                                    "
                                    @click="execute"
                                    class="button-secondary button-lg bg-tint-300 hover:bg-tint-400"
                                >
                                    {{ solution.run_button_text }}
                                </button>
                                <p v-if="executionSuccessful">
                                    <strong class="font-semibold"
                                        >The solution was executed succesfully.</strong
                                    >
                                    <a href="#" @click.prevent="refresh" class="link-solution"
                                        >Refresh now.</a
                                    >
                                </p>
                                <p v-if="executionSuccessful === false">
                                    Something went wrong when executing the solution. Please try
                                    refresh the page and try again.
                                </p>
                            </div>
                        </div>

                        <div
                            class="mt-8 grid justify-start"
                            v-if="Object.entries(solution.links).length > 0"
                        >
                            <div class="border-t-2 border-gray-700 opacity-25 " />
                            <div class="pt-2 grid cols-auto-1fr gapx-4 gapy-2 text-sm">
                                <label class="font-semibold uppercase tracking-wider"
                                    >Read more</label
                                >
                                <ul>
                                    <li v-for="(link, label) in solution.links" :key="label">
                                        <a :href="link" class="link-solution" target="_blank">{{
                                            label
                                        }}</a>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
const MarkdownIt = require('markdown-it')();

const cookieName = 'hide_solutions';
let animationTimeout = null;

export default {
    inject: ['config'],

    props: {
        solution: { required: true },
    },

    data() {
        return {
            isHidingSolutions: this.hasHideSolutionsCookie(),
            canExecuteSolutions: null,
            executionSuccessful: null,
        };
    },

    computed: {
        healthCheckEndpoint() {
            return this.solution.execute_endpoint.replace('execute-solution', 'health-check');
        },
    },

    created() {
        this.configureRunnableSolutions();
    },

    mounted() {
        if (this.isHidingSolutions) {
            this.$refs.solutionCard.classList.add('solution-hidden');
        }
    },

    methods: {
        configureRunnableSolutions() {
            if (!this.config.enableRunnableSolutions) {
                this.canExecuteSolutions = false;

                return;
            }

            this.checkExecutionEndpoint();
        },

        markdown(string) {
            return MarkdownIt.render(string);
        },

        async checkExecutionEndpoint() {
            try {
                const healthCheck = await (await fetch(this.healthCheckEndpoint)).json();

                this.canExecuteSolutions = healthCheck.can_execute_commands;
            } catch (error) {
                this.canExecuteSolutions = false;
            }
        },

        async execute() {
            try {
                const response = await fetch(this.solution.execute_endpoint, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        Accept: 'application/json',
                    },
                    body: JSON.stringify({
                        solution: this.solution.class,
                        parameters: this.solution.run_parameters,
                    }),
                });

                this.executionSuccessful = response.status === 200;
            } catch (error) {
                console.error(error);
                this.executionSuccessful = false;
            }
        },

        refresh() {
            location.reload();
        },

        getUrlLabel(url) {
            const tempElement = document.createElement('a');

            tempElement.href = url;

            return tempElement.hostname;
        },

        toggleSolutions() {
            if (!this.isHidingSolutions) {
                this.$refs.solutionCard.classList.add('solution-hiding');
                animationTimeout = window.setTimeout(() => {
                    this.$refs.solutionCard.classList.remove('solution-hiding');
                    this.toggleHidingSolutions();
                }, 100);
            } else {
                window.clearTimeout(animationTimeout);
                this.toggleHidingSolutions();
            }
        },

        toggleHidingSolutions() {
            if (this.isHidingSolutions) {
                document.cookie = `${cookieName}=;expires=Thu, 01 Jan 1970 00:00:00 UTC;path=/;`;

                this.isHidingSolutions = false;
                return;
            }

            const expires = new Date();
            expires.setTime(expires.getTime() + 365 * 24 * 60 * 60 * 1000);

            document.cookie = `${cookieName}=true;expires=${expires.toUTCString()};path=/;`;

            this.isHidingSolutions = true;
        },

        hasHideSolutionsCookie() {
            return document.cookie.includes(cookieName);
        },
    },
};
</script>
