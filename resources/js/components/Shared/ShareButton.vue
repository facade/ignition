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
