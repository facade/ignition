<template>
    <Stack
        :frames="report.stacktrace"
        :selected-frame-number="selectedFrameNumber"
        @frameclick="selectedFrameNumber = $event"
    />
</template>

<script>
import Stack from '../Stack/Stack.vue';
import findKey from 'lodash/findKey';

export default {
    inject: ['report'],

    props: {
        file: { required: false },
        lineNumber: { required: false },
    },

    data() {
        return {
            selectedFrameNumber: this.report.stacktrace.length,
        };
    },

    components: {
        Stack,
    },

    provide() {
        return {
            setSelectedFrameNumber: frameNumber => (this.selectedFrameNumber = frameNumber),
        };
    },

    created() {
        this.selectFrame();
    },

    computed: {
        selectedFrame() {
            return this.report.stacktrace[this.report.stacktrace.length - this.selectedFrameNumber];
        },
    },

    methods: {
        selectFrame() {
            if (this.file) {
                const frameKey = findKey(this.report.stacktrace, f => f.file === this.file);

                this.selectedFrameNumber =
                    this.report.stacktrace.length - frameKey || this.selectedFrameNumber;
            }
        },
    },
};
</script>
