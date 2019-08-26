<template>
    <span :is="tagName" v-if="className">
        <span class="font-medium">{{ className }}</span
        ><span class="text-600">@{{ methodName }}:{{ lineNumber }}</span>
    </span>
    <span :is="tagName" v-else>
        <span class="font-medium">{{ methodName }}</span
        ><span span class="text-600">:{{ lineNumber }}</span>
    </span>
</template>

<script>
export default {
    props: {
        className: { default: null },
        methodName: { required: true },
        lineNumber: { required: true },
        tagName: { default: 'span' },
    },
};
</script>
