<template>
    <div :class="className" class="definition-list">
        <h3 v-if="title" class="definition-list-title">{{ title }}</h3>
        <dl v-if="this.$slots.default" class="definition-list-items">
            <slot></slot>
        </dl>
        <div v-if="!this.$slots.default" class="definition-list-empty">—</div>
    </div>
</template>

<script>
export default {
    props: {
        title: { default: '' },
        className: { default: '' },
    },
};
</script>
