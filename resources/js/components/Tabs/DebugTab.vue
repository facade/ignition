<template>
    <div class="tab-content">
        <div
            class="sticky top-0 z-10 grid cols-auto items-center justify-center px-6 py-2 bg-gray-100 border-b border-tint-200 text-xs"
        >
            <nav class="grid cols-auto items-center gapx-6 gapy-2">
                <CheckboxField
                    label="Dumps"
                    v-model="visibleTypes.dump"
                    name="show_dumps"
                    :disabled="!dumps.length"
                />

                <CheckboxField
                    label="Glows"
                    v-model="visibleTypes.glow"
                    name="show_glows"
                    :disabled="!glows.length"
                />

                <CheckboxField
                    label="Logs"
                    v-model="visibleTypes.log"
                    name="show_logs"
                    :disabled="!logs.length"
                />

                <CheckboxField
                    label="Queries"
                    v-model="visibleTypes.query"
                    name="show_queries"
                    :disabled="!queries.length"
                />

                <button
                    v-if="hasFilteredVisibleTypes"
                    @click="resetVisibleTypes"
                    class="link-dimmed no-underline absolute left-full ml-6 hidden | sm:block"
                >
                    Reset&nbsp;filters
                </button>
            </nav>
        </div>

        <div class="layout-col">
            <DebugEvent
                v-for="event in visibleTimelineEvents"
                :key="event.microtime"
                v-bind="{ event }"
            ></DebugEvent>
        </div>

        <p
            v-if="visibleTimelineEvents.length === 0"
            class="absolute inset-0 grid place-center alert-empty"
        >
            No debug data available.
        </p>
    </div>
</template>
<script>
import Event from '../DebugTimeline/Event';
import DebugEvent from '../DebugTimeline/DebugEvent.vue';
import CheckboxField from '../Shared/CheckboxField';
import _ from 'lodash';

export default {
    inject: ['report'],

    components: { CheckboxField, DebugEvent },

    data() {
        return {
            visibleTypes: {
                query: true,
                dump: true,
                log: true,
                glow: true,
            },
        };
    },

    computed: {
        queries() {
            return this.report.context.queries || [];
        },
        dumps() {
            return this.report.context.dumps;
        },
        logs() {
            return this.report.context.logs;
        },
        glows() {
            return this.report.glows;
        },
        timelineEvents() {
            return _.sortBy(
                [
                    ...this.queries.map(query => Event.forQuery(query)),
                    ...this.dumps.map(dump => Event.forDump(dump)),
                    ...this.logs.map(log => Event.forLog(log)),
                    ...this.glows.map(glow => Event.forGlow(glow)),
                ],
                e => e.microtime,
            );
        },
        visibleTimelineEvents() {
            return this.timelineEvents.filter(e => {
                return this.visibleTypes[e.type];
            });
        },
        hasFilteredVisibleTypes() {
            let visibleCount = Object.values(this.visibleTypes).filter(type => type).length;
            let totalCount = Object.values(this.visibleTypes).length;

            return visibleCount !== totalCount;
        },
    },

    methods: {
        resetVisibleTypes() {
            this.visibleTypes = _.mapValues(this.visibleTypes, () => true);
        },
    },
};
</script>
