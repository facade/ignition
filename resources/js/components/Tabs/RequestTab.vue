<template>
    <div class="tab-content">
        <div class="layout-col">
            <DefinitionList title="Request" class-name="tab-content-section border-none">
                <DefinitionListRow label="URL">{{ request.url }}</DefinitionListRow>
                <DefinitionListRow label="Method">{{ request.method }}</DefinitionListRow>
            </DefinitionList>

            <DefinitionList title="Headers" class-name="tab-content-section">
                <DefinitionListRow v-for="(key, value) in headers" :key="key" :label="value">{{
                    key[0]
                }}</DefinitionListRow>
            </DefinitionList>

            <DefinitionList title="Query String" class-name="tab-content-section">
                <DefinitionListRow
                    v-for="(key, value) in requestData.queryString"
                    :key="key"
                    :label="value"
                    >{{ key }}</DefinitionListRow
                >
            </DefinitionList>

            <DefinitionList title="Body" class-name="tab-content-section">
                <DefinitionListRow
                    v-for="(key, value) in requestData.body"
                    :key="key"
                    :label="value"
                    >{{ key }}</DefinitionListRow
                >
            </DefinitionList>

            <DefinitionList title="Files" class-name="tab-content-section">
                <DefinitionListRow
                    v-for="(key, value) in requestData.files"
                    :key="key"
                    :label="value"
                    >{{ key }}</DefinitionListRow
                >
            </DefinitionList>

            <DefinitionList title="Session" class-name="tab-content-section">
                <DefinitionListRow v-for="(key, value) in session" :key="key" :label="value">
                    <template v-if="typeof key === 'string'">
                        {{ key }}
                    </template>
                    <code v-else class="code-inline">
                        <pre>{{ key }}</pre>
                    </code>
                </DefinitionListRow>
            </DefinitionList>

            <DefinitionList title="Cookies" class-name="tab-content-section">
                <DefinitionListRow v-for="(key, value) in cookies" :key="key" :label="value">{{
                    key
                }}</DefinitionListRow>
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
        request() {
            return this.report.context.request;
        },

        requestData() {
            return this.report.context.request_data;
        },

        headers() {
            return this.report.context.headers;
        },

        session() {
            return this.report.context.session;
        },

        cookies() {
            return this.report.context.cookies;
        },
    },
};
</script>
