<template>
    <li
        v-if="!frameGroup.expanded && frameGroup.type === 'vendor'"
        class="stack-frame-group stack-frame-group-vendor"
        @click="$emit('expand')"
    >
        <div class="stack-frame | cursor-pointer">
            <button class="stack-frame-number">
                <i class="fas fa-plus-circle text-gray-500" />
            </button>
            <div class="span-2 stack-frame-text">
                <button class="text-left text-gray-500">
                    {{
                        frameGroup.frames.length > 1
                            ? `${frameGroup.frames.length} vendor frames…`
                            : '1 vendor frame…'
                    }}
                </button>
            </div>
        </div>
    </li>
    <li
        v-else-if="frameGroup.type === 'unknown'"
        class="stack-frame-group stack-frame-group-unknown"
    >
        <div class="stack-frame">
            <button class="stack-frame-number"></button>
            <div class="span-2 stack-frame-text">
                <span class="text-left text-gray-500">
                    {{
                        frameGroup.frames.length > 1
                            ? `${frameGroup.frames.length} unknown frames`
                            : '1 unknown frame'
                    }}
                </span>
            </div>
        </div>
    </li>
    <li v-else>
        <ul
            class="stack-frame-group"
            :class="frameGroup.type === 'vendor' ? 'stack-frame-group-vendor' : ''"
        >
            <li
                v-for="(frame, i) in frameGroup.frames"
                :key="i"
                class="stack-frame | cursor-pointer"
                :class="frame.selected ? 'stack-frame-selected' : ''"
                @click="$emit('select', frame.frame_number)"
            >
                <div class="stack-frame-number">{{ frame.frame_number }}</div>
                <div class="stack-frame-text">
                    <header
                        v-if="i === 0"
                        class="stack-frame-header"
                        :class="frame.class ? 'mb-1' : ''"
                    >
                        <FilePath
                            :pathClass="
                                frameGroup.type === 'vendor' ? 'text-gray-800' : 'text-purple-800'
                            "
                            class="stack-frame-path"
                            :file="frame.relative_file"
                        />
                    </header>
                    <span v-if="frame.class" class="stack-frame-exception-class">
                        <ExceptionClass class="stack-frame-exception-class" :name="frame.class" />
                    </span>
                </div>
                <div class="stack-frame-line">
                    <LineNumber :lineNumber="frame.line_number" />
                </div>
            </li>
        </ul>
    </li>
</template>

<script>
import ExceptionClass from '../Shared/ExceptionClass.vue';
import FilePath from '../Shared/FilePath.vue';
import LineNumber from '../Shared/LineNumber.vue';

export default {
    props: {
        frameGroup: { required: true },
    },

    components: {
        ExceptionClass,
        FilePath,
        LineNumber,
    },
};
</script>
