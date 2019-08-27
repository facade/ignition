<template>
    <div class="block">
        <p class="pb-4 text-2xl">
            <span>{{ solution.title }}</span>
        </p>
        <p class="pb-8 text-xl" v-if="solution.description">
            {{ solution.description }}
        </p>
        <div
            class="md:grid  | content-start"
            style="grid-template-columns:calc(500px - 2rem) calc(100% - 500px + 2rem)"
        >
            <div>
                <h3 class="font-semibold uppercase text-sm opacity-50">Learn more</h3>
                <ul class="mt-3 text-sm">
                    <li :key="title" v-for="(url, title) in solution.documentation">
                        <a :href="url" target="_blank">
                            <span class="font-semibold underline">{{ title }}</span>
                            ({{ getUrlLabel(url) }})
                        </a>
                    </li>
                </ul>
            </div>
            <div v-if="solution.is_runnable === true">
                <div class="pt-8 | md:border-l md:border-green-200 md:pl-8 md:pt-0">
                    <h3 class="font-semibold uppercase text-sm opacity-50">Try this first</h3>
                    <p v-html="solution.action_description"></p>
                    <p v-if="canExecuteSolutions === null" class="py-3 text-sm italic">
                        Loading...
                    </p>
                    <button
                        v-if="
                            solution.is_runnable &&
                                canExecuteSolutions === true &&
                                executionSuccessful === null
                        "
                        @click="execute"
                        class="justify-self-start font-bold bg-green-500 text-lg text-white rounded shadow py-3 px-6"
                    >
                        {{ solution.run_button_text }}
                    </button>
                    <p v-if="executionSuccessful">
                        The solution was executed succesfully.
                        <a href="#" @click.prevent="refresh" class="underline">Refresh now.</a>
                    </p>
                    <p v-if="executionSuccessful === false">
                        Something went wrong when executing the solution. Please try refresh the
                        page and try again.
                    </p>
                </div>
            </div>
        </div>
    </div>
</template>
<script>
export default {
    props: {
        solution: { required: true },
    },

    data() {
        return {
            canExecuteSolutions: null,
            healthCheckEndpoint: this.solution.execute_endpoint.replace(
                'execute-solution',
                'health-check',
            ),
            executionSuccessful: null,
        };
    },

    created() {
        this.checkExecutionEndpoint();
    },

    methods: {
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
    },
};
</script>
