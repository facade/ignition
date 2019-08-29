<template>
    <div class="card-details-overflow scrollbar p-12 pt-10">
        <div class="text-2xl">
            <ExceptionClass :name="report.exception_class" />
            <ExceptionMessage :name="report.message" />
        </div>

        <div>
            <a class="ui-url" :href="report.context.request.url" target="_blank">
                {{ report.context.request.url }}
            </a>
        </div>
    </div>
</template>

<script>
import FilePath from './FilePath';
import LineNumber from './LineNumber';
import ExceptionMessage from './ExceptionMessage';
import ExceptionClass from './ExceptionClass';

export default {
    components: { ExceptionClass, ExceptionMessage, LineNumber, FilePath },
    inject: ['report'],

    computed: {
        firstFrame() {
            return this.report.stacktrace[0];
        },
    },
};
</script>
