<template>
    <div>
        <DefinitionList title="Log">
            <DefinitionListRow label="Message"
                ><code class="code-block"
                    ><pre>{{ event.label }}</pre></code
                ></DefinitionListRow
            >
            <DefinitionListRow label="Level">
                {{ event.metadata.level }}
            </DefinitionListRow>
            <DefinitionListRow v-for="(value, key) in event.context" :key="key" :label="key">
                {{ value }}
            </DefinitionListRow>
        </DefinitionList>
    </div>
</template>

<script>
import DefinitionList from '../../Shared/DefinitionList';
import DefinitionListRow from '../../Shared/DefinitionListRow.js';

export default {
    components: { DefinitionListRow, DefinitionList },

    props: ['event'],
};
</script>
