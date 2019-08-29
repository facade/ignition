<template>
    <div>
        <div class="grid cols-2 justify-start gapx-6 gapy-2">
            <CheckboxField
                :key="tab.name"
                v-for="tab in tabs"
                :label="tab.label"
                :name="tab.name"
                v-model="tab.checked"
                @change="tab.checked = !tab.checked"
                class="text-gray-200 hover:text-white"
            />
        </div>

        <div class="mt-4">
            <div class="mb-3" v-if="error">
                We were unable to share your error.<br />
                Please try again later.
            </div>

            <button @click="shareError" class="button-secondary button-sm bg-tint-600 text-white">
                Share
            </button>
        </div>
    </div>
</template>

<script>
import CheckboxField from './CheckboxField';

export default {
    components: { CheckboxField },

    props: ['error'],

    computed: {
        selectedTabs() {
            return this.tabs.filter(tab => tab.checked).map(tab => tab.name);
        },
    },

    data() {
        return {
            tabs: [
                { label: 'Stack trace', name: 'stackTraceTab', checked: true },
                { label: 'Request', name: 'requestTab', checked: true },
                { label: 'App', name: 'appTab', checked: true },
                { label: 'User', name: 'userTab', checked: true },
                { label: 'Context', name: 'contextTab', checked: true },
                { label: 'Debug', name: 'debugTab', checked: true },
            ],
        };
    },

    methods: {
        shareError() {
            this.$emit('share', this.selectedTabs);
        },
    },
};
</script>
