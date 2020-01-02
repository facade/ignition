<template>
    <svg class="icon">
        <use :xlink:href="'#' + name + '-icon'" />
    </svg>
</template>

<script>
export default {
    props: {
        name: {
            required: true,
        },
    },
};
</script>
