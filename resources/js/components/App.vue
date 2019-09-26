<template>
    <div>
        <Summary />
        <div class="layout-col mt-12">
            <div class="tabs">
                <Tabs v-model="tab" v-bind="{ customTabs }" />
                <div class="tab-main">
                    <Details v-bind="{ tab }" />
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import Summary from './Summary.vue';
import Tabs from './Tabs.vue';
import Details from './Details.vue';

export default {
    props: {
        report: { required: true },
        config: { required: true },
        solutions: { required: true },
        telescopeUrl: { required: true },
        shareEndpoint: { required: true },
        defaultTab: { required: true },
        defaultTabProps: { required: false },
    },

    data() {
        return {
            customTabs: window.tabs,
            tab: {
                component: this.defaultTab,
                props: this.defaultTabProps || {},
            },
        };
    },

    provide() {
        return {
            config: this.config,
            report: this.report,
            solutions: this.solutions,
            telescopeUrl: this.telescopeUrl,
            shareEndpoint: this.shareEndpoint,
            setTab: this.setTab,
        };
    },

    components: {
        Summary,
        Tabs,
        Details,
    },

    methods: {
        setTab(component, props = {}) {
            this.tab = {
                component,
                props,
            };
        },
    },

    created() {},
};
</script>
