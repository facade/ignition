<template>
    <div class="mt-12 card card-has-header card-no-props">
        <div class="card-header">
            <div
                class="grid items-center rounded-t border-b border-tint-200 text-xs text-tint-500  "
                style="grid-template-columns: 1fr 1fr;"
            >
                <div class="grid cols-auto justify-start gap-2 px-4 py-2">
                    <div class="flex items-center">
                        <a href="https://flareapp.io" target="_blank">
                            <svg class="w-4 h-5 mr-4" viewBox="0 0 682 1024">
                                <polygon
                                    style="fill:#51DB9E"
                                    points="235.3,510.5 21.5,387 21.5,140.2 236.5,264.1 "
                                />
                                <polygon
                                    style="fill:#7900F5"
                                    points="235.3,1004.8 21.5,881.4 21.5,634.5 234.8,757.9 "
                                />
                                <polygon
                                    style="fill:#94F2C8"
                                    points="448.9,386.9 21.5,140.2 235.3,16.7 663.2,263.4 "
                                />
                                <polygon
                                    style="fill:#A475F4"
                                    points="234.8,757.9 21.5,634.5 235.3,511 449.1,634.5 "
                                />
                            </svg>
                        </a>
                        <FilePath
                            pathClass="font-normal text-gray-600"
                            :file="report.application_path + '/'"
                            :relative="false"
                        />
                    </div>
                </div>
                <div class="grid cols-auto items-center justify-end gap-4 px-4 py-2">
                    <div v-if="telescopeUrl">
                        <!--                <a class="underline" href="/">Flare</a>-->
                        <!--                <span class="opacity-50"> • </span>-->
                        <a :href="telescopeUrl" class="link-dimmed sm:ml-6" target="_blank"
                            >Telescope</a
                        >
                    </div>
                </div>
            </div>
        </div>
        <div />
        <div class="card-details">
            <OccurrenceDetails />
        </div>
    </div>
</template>

<script>
import OccurrenceDetails from './OccurrenceDetails';
import FilePath from './FilePath.vue';

export default {
    inject: ['report', 'telescopeUrl'],

    components: {
        OccurrenceDetails,
        FilePath,
    },
};
</script>
