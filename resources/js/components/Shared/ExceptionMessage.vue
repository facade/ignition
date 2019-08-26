<template>
    <div class="ui-exception-message ui-exception-message-full">
        <span
            v-if="tooLong"
            @click="fullException = !fullException"
            class="cursor-pointer select-none"
            :title="name"
        >
            <template v-if="!fullException"
                >{{ name.substring(0, maxLength) }}…</template
            >
            <template v-else>{{ name }}</template>
        </span>
        <template v-else>{{ name }}</template>
    </div>
</template>

<script>
export default {
    props: {
        name: { required: true },
        maxLength: { default: 500 },
    },

    data() {
        return {
            fullException: false,
        };
    },

    computed: {
        tooLong() {
            return this.name.length > this.maxLength;
        },
    },
};
</script>
