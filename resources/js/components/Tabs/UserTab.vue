<template>
    <div class="tab-content">
        <div class="layout-col">
            <DefinitionList title="User Data" class="tab-content-section border-none">
                <DefinitionListRow v-if="user.email" label="Email">{{
                    user.email
                }}</DefinitionListRow>
                <DefinitionListRow label="User data"
                    ><code class="code-inline"
                        ><pre>{{ stringifiedUserData }}</pre></code
                    ></DefinitionListRow
                >
            </DefinitionList>

            <DefinitionList title="Client info" class="tab-content-section">
                <DefinitionListRow label="IP address">{{ request.ip }}</DefinitionListRow>
                <DefinitionListRow label="User agent">{{ request.useragent }}</DefinitionListRow>
            </DefinitionList>
        </div>
    </div>
</template>

<script>
import md5 from 'md5';
import DefinitionList from '../Shared/DefinitionList';
import DefinitionListRow from '../Shared/DefinitionListRow.js';

export default {
    components: { DefinitionListRow, DefinitionList },

    inject: ['report'],

    computed: {
        user() {
            return this.report.context.user;
        },

        request() {
            return this.report.context.request;
        },

        gravatar() {
            if (!this.user.email) {
                return null;
            }

            const size = 80;

            return 'http://www.gravatar.com/avatar/' + md5(this.user.email) + '.jpg?s=' + size;
        },

        stringifiedUserData() {
            return JSON.stringify(this.user, null, 4);
        },
    },
};
</script>
