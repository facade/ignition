<template>
    <div class="text-left">
        <p class="mt-2 text-gray-300">Share your error with others:</p>
        <div class="grid cols-auto items-center justify-start gap-2 mt-2">
            <a
                :href="publicUrl"
                target="_blank"
                class="button-secondary button-sm bg-tint-600 hover:bg-tint-700 text-white"
                >Open public share</a
            >
            <CopyButton :text="publicUrl"> </CopyButton>
        </div>

        <p class="mt-4 text-gray-300">Administer your shared error here:</p>
        <div class="grid cols-auto items-center justify-start gap-2 mt-2">
            <a
                :href="ownerUrl"
                target="_blank"
                class="button-secondary button-sm bg-tint-600 hover:bg-tint-700 text-white"
                >Open share admin</a
            >
            <CopyButton :text="ownerUrl"> </CopyButton>
        </div>
    </div>
</template>

<script>
import CopyButton from './CopyButton';

export default {
    components: { CopyButton },

    props: {
        publicUrl: { required: true },
        ownerUrl: { required: true },
    },
};
</script>
