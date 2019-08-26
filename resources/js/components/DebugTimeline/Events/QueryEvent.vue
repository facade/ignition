<template>
    <div>
        <DefinitionList title="Query">
            <DefinitionListRow label="Query">
                <code class="code-block"><pre v-html="formatQuery(event.label)"></pre></code>
            </DefinitionListRow>
            <DefinitionListRow label="Time">
                {{ event.metadata.time }}
            </DefinitionListRow>
            <DefinitionListRow label="Connection name">
                {{ event.metadata.connection_name }}
            </DefinitionListRow>
            <DefinitionListRow v-for="(value, key) in event.context" :key="key" :label="key">
                {{ value }}
            </DefinitionListRow>
        </DefinitionList>
    </div>
</template>

<script>
import sqlFormatter from 'sql-formatter';
import DefinitionList from '../../Shared/DefinitionList';
import DefinitionListRow from '../../Shared/DefinitionListRow.js';

export default {
    components: { DefinitionListRow, DefinitionList },

    props: ['event'],

    methods: {
        formatQuery(query) {
            return sqlFormatter.format(query);
        },
    },
};
</script>
