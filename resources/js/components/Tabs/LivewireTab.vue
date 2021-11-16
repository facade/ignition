<template>
    <div class="tab-content">
        <div class="layout-col">
            <DefinitionList title="Component" class="tab-content-section">
                <DefinitionListRow
                    v-for="(value, key) in livewire"
                    v-if="key.startsWith('component_')"
                    :key="key"
                    :label="lookupKey(key)"
                    >{{ value }}</DefinitionListRow
                >
            </DefinitionList>
            <DefinitionList title="Updates" class="tab-content-section">
                <DefinitionListRow
                    v-for="(value, key) in livewire.updates"
                    :key="key"
                    :label="value['type']"
                >
                    <DefinitionList>
                        <DefinitionListRow
                            v-for="(parameter, key) in value['payload'] || []"
                            :label="key"
                            :key="key"
                        >
                            <code class="code-inline">
                                <pre>{{ parameter }}</pre>
                            </code>
                        </DefinitionListRow>
                    </DefinitionList>
                </DefinitionListRow>
            </DefinitionList>
            <DefinitionList title="Data" class="tab-content-section">
                <DefinitionListRow v-for="(value, key) in livewire.data" :key="key" :label="key">
                    <template v-if="typeof value === 'string'">
                        {{ value }}
                    </template>
                    <code v-else class="code-inline">
                        <pre>{{ value }}</pre>
                    </code>
                </DefinitionListRow>
            </DefinitionList>
        </div>
    </div>
</template>
<script>
import DefinitionList from '../Shared/DefinitionList';
import DefinitionListRow from '../Shared/DefinitionListRow.js';
import upperFirst from 'lodash/upperFirst';

const predefinedKeys = {
    component_alias: 'Alias',
    component_id: 'Id',
    component_class: 'Class',
};

export default {
    inject: ['report'],

    components: { DefinitionListRow, DefinitionList },

    filters: {
        upperFirst,
    },

    computed: {
        livewire() {
            return this.report.context.livewire;
        },

        data() {
            return this.report.context.livewire.data;
        },

        updates() {
            return this.report.context.livewire.updates;
        },
    },

    methods: {
        lookupKey(key) {
            return predefinedKeys[key] || key;
        },
    },
};
</script>
