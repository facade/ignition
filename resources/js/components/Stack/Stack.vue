<template>
    <div class="stack">
        <div class="stack-nav">
            <div class="stack-nav-actions">
                <div class="stack-nav-arrows">
                    <button
                        @click="selectPreviousFrame"
                        title="Frame up (Key:K)"
                        class="stack-nav-arrow"
                    >
                        <i class="fas fa-arrow-up" />
                    </button>
                    <button
                        @click="selectNextFrame"
                        title="Frame down (Key:J)"
                        class="stack-nav-arrow"
                    >
                        <i class="fas fa-arrow-down" />
                    </button>
                </div>
                <div class="px-4">
                    <button
                        v-if="allVendorFramesAreExpanded"
                        class="ml-auto link-dimmed"
                        @click="collapseAllVendorFrames"
                    >
                        Collapse vendor frames
                    </button>
                    <button v-else class="ml-auto link-dimmed" @click="expandAllVendorFrames">
                        Expand vendor frames
                    </button>
                </div>
            </div>
            <div class="stack-frames">
                <ol class="stack-frames-scroll scrollbar">
                    <FrameGroup
                        v-for="(frameGroup, i) in frameGroups"
                        :key="i"
                        :frameGroup="frameGroup"
                        @expand="expandFrameGroup(frameGroup)"
                        @select="selectFrame"
                    />
                </ol>
            </div>
        </div>
        <Snippet
            :selected-frame="selectedFrame"
            :selected-range="selectedRange"
            @select-range="selectedRange = $event"
        />
    </div>
</template>

<script>
import Snippet from './Snippet.vue';
import FrameGroup from './FrameGroup.vue';
import {
    stackReducer,
    allVendorFramesAreExpanded,
    createFrameGroups,
    getSelectedFrame,
} from '../../stack';

export default {
    props: {
        frames: { required: true },
    },

    data() {
        return {
            state: {
                frames: this.frames,
                expanded: [],
                selected: this.frames.length,
            },
            selectedRange: null,
        };
    },

    components: {
        Snippet,
        FrameGroup,
    },

    created() {
        this.state = stackReducer(this.state, { type: 'COLLAPSE_ALL_VENDOR_FRAMES' });

        this.dispatch = action => {
            this.state = stackReducer(this.state, action);
        };

        const keydownHandler = e => {
            if (e.key === 'j') {
                this.selectNextFrame();
            }

            if (e.key === 'k') {
                this.selectPreviousFrame();
            }
        };

        window.addEventListener('keydown', keydownHandler);

        this.$once('hook:beforeDestroy', () => {
            window.removeEventListener('keydown', keydownHandler);
        });
    },

    computed: {
        allVendorFramesAreExpanded() {
            return allVendorFramesAreExpanded(this.state);
        },

        frameGroups() {
            return createFrameGroups(this.state);
        },

        selectedFrame() {
            return getSelectedFrame(this.state);
        },
    },

    watch: {
        selectedRange(selectedRange) {
            if (selectedRange) {
                const lineNumber =
                    selectedRange[0] === selectedRange[1]
                        ? selectedRange[0]
                        : `${selectedRange[0]}-${selectedRange[1]}`;

                window.history.replaceState(
                    window.history.state,
                    '',
                    `#F${this.state.selected}L${lineNumber}`,
                );
            }
        },
    },

    methods: {
        expandFrameGroup(frameGroup) {
            this.dispatch({
                type: 'EXPAND_FRAMES',
                frames: frameGroup.frames.map(frame => frame.frame_number),
            });
        },

        selectFrame(frame) {
            this.dispatch({ type: 'SELECT_FRAME', frame });
        },

        selectNextFrame() {
            this.dispatch({ type: 'SELECT_NEXT_FRAME' });
        },

        selectPreviousFrame() {
            this.dispatch({ type: 'SELECT_PREVIOUS_FRAME' });
        },

        collapseAllVendorFrames() {
            this.dispatch({ type: 'COLLAPSE_ALL_VENDOR_FRAMES' });
        },

        expandAllVendorFrames() {
            this.dispatch({ type: 'EXPAND_ALL_VENDOR_FRAMES' });
        },
    },
};
</script>
