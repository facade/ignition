<template>
    <div>
        <DefinitionList title="Glow">
            <DefinitionListRow label="Message"
                ><code class="code-block"><pre v-html="event.label"></pre></code
            ></DefinitionListRow>
            <DefinitionListRow label="Level">
                {{ event.metadata.message_level }}
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

    mounted() {
        console.log(this.event);
    },
};
</script>
