<template>
    <div class="tab-content">
        <div class="layout-col">
            <DefinitionList title="Routing" class-name="tab-content-section border-none">
                <DefinitionListRow label="Controller">{{
                    route.controllerAction
                }}</DefinitionListRow>
                <DefinitionListRow label="Route name">{{
                    route.route || 'unknown'
                }}</DefinitionListRow>
                <DefinitionListRow label="Route parameters">
                    <DefinitionList>
                        <DefinitionListRow
                            v-for="(parameter, key) in route.routeParameters || []"
                            :label="key"
                            :key="key"
                            ><code class="code-inline"
                                ><pre>{{ parameter }}</pre></code
                            ></DefinitionListRow
                        >
                    </DefinitionList>
                </DefinitionListRow>
                <DefinitionListRow label="Middleware">
                    <DefinitionList>
                        <DefinitionListRow
                            v-for="(middleware, i) in route.middleware || []"
                            :key="i"
                            >{{ middleware }}</DefinitionListRow
                        >
                    </DefinitionList>
                </DefinitionListRow>
            </DefinitionList>

            <DefinitionList title="View" class="tab-content-section">
                <DefinitionListRow label="View name">{{ view.view }}</DefinitionListRow>
                <DefinitionListRow label="View data">
                    <DefinitionList>
                        <DefinitionListRow
                            v-for="(dump, key) in view.data || []"
                            :key="key"
                            :label="key"
                        >
                            <div v-html="dump"></div>
                        </DefinitionListRow>
                    </DefinitionList>
                </DefinitionListRow>
            </DefinitionList>
        </div>
    </div>
</template>
<script>
import DefinitionList from '../Shared/DefinitionList';
import DefinitionListRow from '../Shared/DefinitionListRow.js';

export default {
    components: { DefinitionListRow, DefinitionList },
    inject: ['report'],

    computed: {
        route() {
            return this.report.context.route;
        },
        view() {
            return this.report.context.view || {};
        },
        viewData() {
            return Object.entries(this.view.data || []).map(([key, dump]) => ({ key, dump }));
        },
    },
};
</script>
