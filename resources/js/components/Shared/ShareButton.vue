<template>
    <div @click.stop>
        <button class="tab" :class="menuVisible ? 'tab-active' : ''" @click="toggleMenu">
            Share
            <i class="ml-2 fas fa-share"></i>
        </button>
        <div
            class="dropdown z-10 right-0 top-full p-4 overflow-visible"
            :class="{ hidden: !menuVisible }"
            @click.stop
            style="min-width: 18rem; margin-right: -1px"
        >
            <div class="flex items-center mb-4">
                <svg class="w-4 h-5 mr-2" viewBox="0 0 682 1024">
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
                <h5 class="text-left font-semibold uppercase tracking-wider whitespace-no-wrap">
                    {{ sharedErrorUrls ? 'Shared' : 'Share' }} on Flare
                </h5>
                <a
                    class="ml-auto underline"
                    target="_blank"
                    href="https://flareapp.io/docs/ignition-for-laravel/sharing-errors"
                    title="Flare documentation"
                    >Docs
                </a>
            </div>
            <div v-if="sharedErrorUrls">
                <ShareLinks
                    :publicUrl="sharedErrorUrls.public_url"
                    :ownerUrl="sharedErrorUrls.owner_url"
                />
            </div>
            <ShareForm v-else @share="shareError" :error="shareHadError" />
        </div>
    </div>
</template>

<script>
import ShareForm from './ShareForm';
import ShareLinks from './ShareLinks';

export default {
    components: { ShareLinks, ShareForm },
    inject: ['report', 'shareEndpoint'],

    data() {
        return {
            shareHadError: false,
            sharedErrorUrls: null,
            menuVisible: false,
        };
    },

    watch: {
        menuVisible(menuVisible) {
            if (menuVisible) {
                window.addEventListener('click', this.toggleMenu);
            } else {
                window.removeEventListener('click', this.toggleMenu);
            }
        },
    },

    methods: {
        toggleMenu() {
            this.menuVisible = !this.menuVisible;
        },

        async shareError(selectedTabs) {
            try {
                const response = await fetch(this.shareEndpoint, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        Accept: 'application/json',
                    },
                    body: JSON.stringify({
                        report: JSON.stringify(this.report),
                        tabs: selectedTabs,
                        lineSelection: window.location.hash,
                    }),
                });
                const responseData = await response.json();

                if (response.ok) {
                    this.sharedErrorUrls = responseData;
                } else {
                    this.shareHadError = true;
                }
            } catch (error) {
                this.shareHadError = true;
            }
        },
    },
};
</script>
