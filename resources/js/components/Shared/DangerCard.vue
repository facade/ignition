<template>
    <div class="mt-12 card card-has-header card-no-props">
        <div class="card-details card-danger">
            <div class="card-details-overflow scrollbar p-4">
                <div class="text-xl">
                    <slot />
                </div>
            </div>
        </div>
    </div>
</template>

<script>
export default {
    inject: ['config'],

    props: ['text'],
};
</script>
