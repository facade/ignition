<template>
    <div class="stack-main">
        <div class="stack-main-header">
            <div class="grid cols-auto gap-2 justify-start items-center">
                <ExceptionClass
                    :name="selectedFrame.class || ''"
                    :method="selectedFrame.method || ''"
                />
                <LineNumber :line-number="selectedFrame.line_number" />
            </div>
            <FilePath
                v-if="selectedFrame.file"
                class="mt-1"
                :line-number="selectedFrame.line_number"
                :file="selectedFrame.file"
                :editable="true"
            ></FilePath>
        </div>
        <div class="stack-main-content">
            <div class="stack-viewer scrollbar">
                <div class="stack-ruler">
                    <div class="stack-lines">
                        <p
                            v-for="(code, line_number) in selectedFrame.code_snippet"
                            :key="line_number"
                            class="stack-line cursor-pointer"
                            :class="{
                                'stack-line-selected': withinSelectedRange(parseInt(line_number)),
                                'stack-line-highlight':
                                    parseInt(line_number) === selectedFrame.line_number,
                            }"
                            @click="handleLineNumberClick($event, parseInt(line_number))"
                        >
                            {{ line_number }}
                        </p>
                    </div>
                </div>
                <pre :class="highlightTheme" class="stack-code" ref="codeContainer"><p
                        v-for="(code, line_number) in selectedFrame.code_snippet"
                        :key="line_number"
                        :class="{
                            'stack-code-line-highlight': parseInt(line_number) === selectedFrame.line_number,
                            'stack-code-line-selected': withinSelectedRange(parseInt(line_number)),
                        }"
                        class="stack-code-line"
                ><span v-html="highlightedCode(code)"></span><a :href="editorUrl(line_number)" class="editor-link"><i class="fa fa-pencil-alt"></i></a></p>
                </pre>
            </div>
        </div>
    </div>
</template>

<script>
import hljs from 'highlight.js/lib/highlight';
hljs.registerLanguage('php', require('highlight.js/lib/languages/php'));

import ExceptionClass from '../Shared/ExceptionClass.vue';
import FilePath from '../Shared/FilePath.vue';
import LineNumber from '../Shared/LineNumber.vue';
import editorUrl from '../Shared/editorUrl';

export default {
    inject: ['config'],

    props: {
        selectedFrame: { required: true },
        selectedRange: { default: [null, null] },
    },

    components: {
        ExceptionClass,
        FilePath,
        LineNumber,
    },

    data() {
        const mediaQuery = matchMedia('(prefers-color-scheme: dark)');

        return {
            mediaQuery,
            detectedTheme: mediaQuery.matches ? 'dark' : 'light',
            firstSelectedLineNumber: null,
        };
    },

    created() {
        this.highlightState = null;
    },

    watch: {
        selectedFrame() {
            this.highlightState = null;
        },
    },

    mounted() {
        this.mediaQuery.addEventListener('change', e => {
            this.detectedTheme = e.matches ? 'dark' : 'light';
        });
    },

    computed: {
        highlightTheme() {
            if (this.config.theme === 'auto') {
                return this.detectedTheme;
            }
            return this.config.theme;
        },
    },

    methods: {
        handleLineNumberClick(event, lineNumber) {
            if (event.shiftKey && this.firstSelectedLineNumber !== null) {
                this.$emit(
                    'select-range',
                    [this.firstSelectedLineNumber, lineNumber].sort((a, b) => a - b),
                );
            } else {
                this.firstSelectedLineNumber = lineNumber;
                this.$emit('select-range', [lineNumber, lineNumber]);
            }
        },
        withinSelectedRange(lineNumber) {
            if (!this.selectedRange) {
                return false;
            }

            return lineNumber >= this.selectedRange[0] && lineNumber <= this.selectedRange[1];
        },
        editorUrl(lineNumber) {
            return editorUrl(this.config, this.selectedFrame.file, lineNumber);
        },
        highlightedCode(code) {
            const result = hljs.highlight('php', code || '', true, this.highlightState);

            this.highlightState = result.top;

            return result.value;
        },
    },
};
</script>
