<template>
    <div class="mt-12 card card-has-header card-no-props">
        <div class="card-header">
            <div
                class="grid items-center rounded-t border-b border-tint-300 text-xs text-tint-600  "
                style="grid-template-columns: 1fr 1fr;"
            >
                <div class="grid cols-auto justify-start gap-2 px-4 py-2">
                    <div class="flex items-center">
                        <a
                            href="http://flareapp.io/docs/ignition-for-laravel/introduction"
                            target="_blank"
                            title="Ignition docs"
                        >
                            <svg class="w-4 h-5 mr-4" viewBox="0 0 428 988">
                                <polygon
                                    style="fill:#FA4E79;"
                                    points="428,247.1 428,494.1 214,617.5 214,369.3 		"
                                />
                                <polygon
                                    style="fill:#FFF082;"
                                    points="0,988 0,741 214,617.5 214,864.1 		"
                                />
                                <polygon
                                    style="fill:#E6003A;"
                                    points="214,123.9 214,617.5 0,494.1 0,0 		"
                                />
                                <polygon
                                    style="fill:#FFE100;"
                                    points="214,864.1 214,617.5 428,741 428,988 		"
                                />
                            </svg>
                        </a>
                        <FilePath
                            pathClass="font-normal"
                            :file="report.application_path + config.directorySeparator"
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
    inject: ['report', 'telescopeUrl', 'config'],

    components: {
        OccurrenceDetails,
        FilePath,
    },
};
</script>
