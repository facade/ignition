<template>
    <span class="ui-line-number">
        :<span class="font-mono">{{ lineNumber }}</span>
    </span>
</template>

<script>
export default {
    props: {
        lineNumber: { required: true },
    },
};
</script>
