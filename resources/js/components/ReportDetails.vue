<template>
    <div class="h-full"></div>
</template>

<script>
import ReportDetailsNav from './ReportDetailsNav.vue';
import ReportDetailsTab from './ReportDetailsTab.vue';

export default {
    data: () => ({
        activeDetailsTab: 'Stack',
    }),

    components: {
        ReportDetailsNav,
        ReportDetailsTab,
    },

    computed: {},
};
</script>
