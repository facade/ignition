<template>
    <span class="inline-flex justify-start items-baseline">
        <span :class="pathClass" class="ui-path" v-on="$listeners">
            <!-- Keep tags tight to prevent whitespace between segments -->
            <span v-for="(segment, index) in segments" :key="`segment-${index}`"
                >{{ segment }}/<wbr /></span
            ><span
                v-for="(fileSegment, index) in fileSegments"
                :key="`file-${index}`"
                :class="index === 0 ? 'font-semibold' : ''"
                >{{ index > 0 ? '.' : '' }}{{ fileSegment }}</span
            >
        </span>
        <slot></slot>
        <a
            v-if="editable && editorUrl"
            :href="editorUrl"
            class="ml-2 inline-block text-sm text-purple-400 hover:text-purple-500"
            ><i class="fas fa-pencil-alt"></i
        ></a>
    </span>
</template>

<script>
import editorUrl from './editorUrl';

export default {
    props: {
        file: { required: true },
        editable: { default: false },
        relative: { default: true },
        lineNumber: { required: false },
        pathClass: { default: '' },
    },

    data() {
        return {
            segments: [],
            filename: '',
            fileSegments: [],
        };
    },

    inject: ['config', 'report'],

    watch: {
        file: {
            immediate: true,
            handler() {
                this.segments = this.path.replace(/^\/Users/, '~').split('/');
                this.filename = this.segments.pop() || '';
                this.fileSegments = this.filename.split('.');
            },
        },
    },

    computed: {
        path() {
            return this.relative
                ? this.file.replace(this.report.application_path + '/', '')
                : this.file;
        },

        editorUrl() {
            return editorUrl(this.config, this.file, this.lineNumber);
        },
    },
};
</script>
