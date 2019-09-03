<template>
    <div class="tab-content">
        <div class="layout-col">
            <DefinitionList
                v-for="(contextGroup, groupTitle) in customContextGroups"
                :key="groupTitle"
                :title="groupTitle"
                class="tab-content-section"
            >
                <DefinitionListRow v-for="(value, key) in contextGroup" :key="key" :label="key">{{
                    value | upperFirst
                }}</DefinitionListRow>
            </DefinitionList>

            <section v-if="git" class="tab-content-section border-none">
                <DefinitionList title="Git">
                    <DefinitionListRow v-if="repoUrl" label="Repository">
                        <a class="underline" :href="repoUrl" target="_blank">{{ repoUrl }}</a>
                    </DefinitionListRow>
                    <DefinitionListRow v-if="git.message" label="Message">
                        <a :href="commitUrl" target="_blank">
                            “{{ git.message }}” –
                            <code class="code underline">{{ git.hash }}</code>
                        </a>
                    </DefinitionListRow>
                    <DefinitionListRow v-if="git.tag" label="Tag">{{ git.tag }}</DefinitionListRow>
                    <div class="mt-4 sm:start-2" v-if="git.isDirty">
                        <div class="inline-block alert alert-warning min-h-0">
                            This commit is dirty. (Un)staged changes have been made since this
                            commit.
                        </div>
                    </div>
                </DefinitionList>
            </section>

            <DefinitionList title="Environment information" class="tab-content-section">
                <DefinitionListRow v-for="(value, key) in env" :key="key" :label="lookupKey(key)">{{
                    value
                }}</DefinitionListRow>
            </DefinitionList>

            <DefinitionList title="Generic context" class="tab-content-section">
                <DefinitionListRow v-for="(value, key) in context" :key="key" :label="key">{{
                    value
                }}</DefinitionListRow>
            </DefinitionList>
        </div>
    </div>
</template>
<script>
import gitUrlParse from 'git-url-parse';
import DefinitionList from '../Shared/DefinitionList';
import DefinitionListRow from '../Shared/DefinitionListRow.js';
import upperFirst from 'lodash/upperFirst';

const predefinedKeys = {
    laravel_version: 'Laravel version',
    laravel_locale: 'Laravel locale',
    laravel_config_cached: 'Laravel config cached',
    php_version: 'PHP version',
};

const predefinedContextItemGroups = [
    'request',
    'request_data',
    'headers',
    'session',
    'cookies',
    'view',
    'queries',
    'route',
    'user',
    'env',
    'git',
    'context',
    'logs',
    'dumps',
];

export default {
    inject: ['report'],

    components: { DefinitionListRow, DefinitionList },

    filters: {
        upperFirst,
    },

    computed: {
        git() {
            return this.report.context.git;
        },

        env() {
            return this.report.context.env;
        },

        context() {
            return this.report.context.context;
        },

        repoInfo() {
            return gitUrlParse(this.git.remote);
        },

        repoUrl() {
            if (!this.git.remote) {
                return null;
            }

            const git = {
                ...this.repoInfo,
                git_suffix: false,
            };

            return gitUrlParse.stringify(git, 'https');
        },

        commitUrl() {
            return `${this.repoUrl}/commit/${this.git.hash}`;
        },

        tagUrl() {
            return this.git.tag ? `${this.repoUrl}/releases/tag/${this.git.tag}` : this.repoUrl;
        },

        customContextGroups() {
            const customGroups = Object.keys(this.report.context).filter(
                key => !predefinedContextItemGroups.includes(key),
            );

            return Object.assign(
                {},
                ...customGroups.map(prop => {
                    return {
                        [prop]: this.report.context[prop],
                    };
                }),
            );
        },
    },

    methods: {
        lookupKey(key) {
            return predefinedKeys[key] || key;
        },
    },
};
</script>
