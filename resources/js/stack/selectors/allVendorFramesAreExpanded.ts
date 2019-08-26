import { State } from '../types';
import { addFrameNumbers, getFrameType } from '../helpers';

export default function allVendorFramesAreExpanded(state: State) {
    return addFrameNumbers(state.frames)
        .filter(frame => getFrameType(frame) === 'vendor')
        .every(frame => state.expanded.includes(frame.frame_number));
}
