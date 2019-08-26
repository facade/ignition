import { Frame, FrameType } from './types';

export function addFrameNumbers(frames: Array<Frame>): Array<Frame & { frame_number: number }> {
    return frames.map((frame, i) => ({
        ...frame,
        frame_number: frames.length - i,
    }));
}

export function getFrameType(frame: Frame): FrameType {
    if (frame.relative_file.startsWith('vendor/')) {
        return 'vendor';
    }

    if (frame.relative_file === 'unknown') {
        return 'unknown';
    }

    return 'application';
}
