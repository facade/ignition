<template>
    <div :class="className">
        <h3 v-if="title" class="definition-list-title">{{ title }}</h3>
        <dl v-if="this.$slots.default" class="definition-list">
            <slot></slot>
        </dl>
        <div v-if="!this.$slots.default" class="definition-list">
            <div class="definition-list-empty">—</div>
        </div>
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
