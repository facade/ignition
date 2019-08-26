<template>
    <label
        :for="name"
        class="checkbox-label"
        :class="disabled ? 'text-gray-400 pointer-events-none' : ''"
    >
        <input
            :id="name"
            :checked="value"
            @change="checkboxChanged"
            type="checkbox"
            class="checkbox"
            :disabled="disabled"
        />
        {{ label }}
    </label>
</template>

<script>
export default {
    props: {
        label: { required: true },
        name: { required: true },
        disabled: { required: false },
        value: { required: false, default: false },
    },

    methods: {
        checkboxChanged(event) {
            this.$emit('input', event.target.checked);
        },
    },
};
</script>
