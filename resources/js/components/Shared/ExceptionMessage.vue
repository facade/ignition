<template>
    <div
        class="ui-exception-message"
        :class="fullException ? 'ui-exception-message-full' : ''"
        @mousedown="removeClamp"
    >
        {{ name }}
    </div>
</template>

<script>
export default {
    props: {
        name: { required: true },
    },

    data() {
        return {
            fullException: false,
        };
    },

    methods: {
        removeClamp() {
            if (!this.fullException) {
                this.fullException = true;
            }
        },
    },
};
</script>
