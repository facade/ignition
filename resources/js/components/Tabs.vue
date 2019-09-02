<template>
    <nav class="tab-nav">
        <ul class="tab-bar">
            <li v-for="tab in tabs" :key="tab.component">
                <button
                    class="tab"
                    :class="value.component === tab.component ? 'tab-active' : ''"
                    @click.prevent="$emit('input', tab)"
                >
                    {{ tab.title }}
                </button>
            </li>
        </ul>
        <template v-if="shareButtonEnabled">
            <div class="tab-delimiter" />
            <ShareButton />
        </template>
    </nav>
</template>

<script>
import ShareButton from './Shared/ShareButton';

export default {
    inject: ['config'],
    components: { ShareButton },
    props: {
        value: { required: true },
        customTabs: { required: true },
    },

    data() {
        return {
            defaultTabs: [
                {
                    component: 'StackTab',
                    title: 'Stack trace',
                },
                {
                    component: 'RequestTab',
                    title: 'Request',
                },
                {
                    component: 'AppTab',
                    title: 'App',
                },
                {
                    component: 'UserTab',
                    title: 'User',
                },
                {
                    component: 'ContextTab',
                    title: 'Context',
                },
                {
                    component: 'DebugTab',
                    title: 'Debug',
                },
            ],
            shareButtonEnabled: this.config.enableShareButton,
        };
    },

    mounted() {
        this.$emit('input', this.tabs[this.currentTabIndex]);
    },

    computed: {
        currentTabIndex() {
            return this.tabs.findIndex(tab => tab.component === this.value.component);
        },

        nextTab() {
            return this.tabs[this.currentTabIndex + 1] || this.tabs[0];
        },

        previousTab() {
            return this.tabs[this.currentTabIndex - 1] || this.tabs[this.tabs.length - 1];
        },

        tabs() {
            let tabs = {};

            this.defaultTabs.forEach(tab => {
                tabs[tab.component] = tab;
            });

            this.customTabs.forEach(tab => {
                tabs[tab.component] = tab;
            });

            return Object.values(tabs);
        },
    },
};
</script>
