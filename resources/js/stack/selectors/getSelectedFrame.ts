import { State, Frame } from '../types';
import { addFrameNumbers } from '../helpers';

export default function getSelectedFrame(state: State): Frame {
    return addFrameNumbers(state.frames).find(
        frame => frame.frame_number === state.selected,
    ) as Frame;
}
