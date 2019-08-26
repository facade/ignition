import { State, Frame, FrameGroup } from '../types';
import { getFrameType } from '../helpers';

type IterationContext = {
    current: Frame;
    previous: FrameGroup;
    isFirstFrame: boolean;
    frameNumber: number;
    expanded: Array<number>;
    selected: number;
};

const dummyFrameGroup: FrameGroup = {
    type: 'application',
    relative_file: '',
    expanded: true,
    frames: [],
};

export default function createFrameGroups({
    frames,
    selected,
    expanded,
}: State): Array<FrameGroup> {
    return frames.reduce(
        (frameGroups, current, i) => {
            const context: IterationContext = {
                current,
                previous: frameGroups[frameGroups.length - 1] || dummyFrameGroup,
                isFirstFrame: i === 0,
                frameNumber: frames.length - i,
                expanded,
                selected,
            };

            if (context.expanded.includes(context.frameNumber)) {
                return frameGroups.concat(parseExpandedFrame(context));
            }

            return frameGroups.concat(parseCollapsedFrame(context));
        },
        [] as Array<FrameGroup>,
    );
}

function parseExpandedFrame(context: IterationContext): Array<FrameGroup> {
    if (context.current.relative_file !== context.previous.relative_file) {
        return [
            {
                type: getFrameType(context.current),
                relative_file: context.current.relative_file,
                expanded: true,
                frames: [
                    {
                        ...context.current,
                        frame_number: context.frameNumber,
                        selected: context.selected === context.frameNumber,
                    },
                ],
            },
        ];
    }

    context.previous.frames.push({
        ...context.current,
        frame_number: context.frameNumber,
        selected: context.selected === context.frameNumber,
    });

    return [];
}

function parseCollapsedFrame(context: IterationContext): Array<FrameGroup> {
    const type = getFrameType(context.current);

    if (!context.previous.expanded && type === context.previous.type) {
        // Mutate the previous result. It's not pretty, makes the general flow of the program less
        // complex because we kan keep the result list append-only.
        context.previous.frames.push({
            ...context.current,
            selected: false,
            frame_number: context.frameNumber,
        });

        return [];
    }

    return [
        {
            type,
            relative_file: context.current.relative_file,
            expanded: false,
            frames: [
                {
                    ...context.current,
                    frame_number: context.frameNumber,
                    selected: context.selected === context.frameNumber,
                },
            ],
        },
    ];
}
