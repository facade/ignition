<template>
    <div>
        <DefinitionList title="Dump">
            <DefinitionListRow label="Content"
                ><code class="code-block mb-3" v-html="event.label"></code>
            </DefinitionListRow>
            <DefinitionListRow label="Location"
                ><FilePath
                    v-if="event.file"
                    :file="event.file"
                    :lineNumber="event.line_number"
                    :editable="true"
                ></FilePath>
            </DefinitionListRow>
        </DefinitionList>
    </div>
</template>

<script>
import FilePath from '../../Shared/FilePath';
import DefinitionList from '../../Shared/DefinitionList';
import DefinitionListRow from '../../Shared/DefinitionListRow.js';

export default {
    components: { DefinitionListRow, DefinitionList, FilePath },

    inject: ['setTab'],

    props: ['event'],

    mounted() {
        let dumpId = this.detectDumpId(this.event.label);

        if (dumpId) {
            window.Sfdump(dumpId);
        }
    },

    methods: {
        detectDumpId(dumpHtml) {
            const pattern = /sf-dump-([0-9]+)/gm;
            const matches = pattern.exec(dumpHtml);

            return matches[0] || null;
        },

        openInStackTab() {
            this.setTab('StackTab', {
                file: this.event.file,
                lineNumber: this.event.line_number,
            });
        },
    },
};
</script>
