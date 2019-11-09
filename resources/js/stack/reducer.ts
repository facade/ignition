import uniq from 'lodash/uniq';
import { State, Action } from './types';
import { addFrameNumbers } from './helpers';

export default function stackReducer(state: State, action: Action): State {
    switch (action.type) {
        case 'EXPAND_FRAMES': {
            const expanded = uniq([...state.expanded, ...action.frames]);

            return { ...state, expanded };
        }
        case 'EXPAND_ALL_VENDOR_FRAMES': {
            const knownFrameNumbers = addFrameNumbers(state.frames)
                .filter(frame => frame.relative_file !== 'unknown')
                .map(frame => frame.frame_number);

            return { ...state, expanded: knownFrameNumbers };
        }
        case 'COLLAPSE_ALL_VENDOR_FRAMES': {
            const applicationFrameNumbers = addFrameNumbers(state.frames)
                .filter(frame => {
                    if (frame.relative_file.startsWith('vendor/')) {
                        return false;
                    }

                    if (frame.relative_file.startsWith('vendor\\')) {
                        return false;
                    }

                    if (frame.relative_file !== 'unknown') {
                        return false;
                    }

                    return true;
                })
                .map(frame => frame.frame_number);

            const expanded = uniq([...applicationFrameNumbers, state.frames.length]);

            return { ...state, expanded };
        }
        case 'SELECT_FRAME': {
            const selectableFrameNumbers = addFrameNumbers(state.frames)
                .filter(frame => frame.relative_file !== 'unknown')
                .map(frame => frame.frame_number);

            const selected = selectableFrameNumbers.includes(action.frame)
                ? action.frame
                : state.selected;

            const expanded = uniq([...state.expanded, selected]);

            return { ...state, expanded, selected };
        }
        case 'SELECT_NEXT_FRAME': {
            const selectableFrameNumbers = addFrameNumbers(state.frames)
                .filter(frame => frame.relative_file !== 'unknown')
                .map(frame => frame.frame_number);

            const selectedIndex = selectableFrameNumbers.indexOf(state.selected);

            const selected =
                selectedIndex === selectableFrameNumbers.length - 1
                    ? selectableFrameNumbers[0]
                    : selectableFrameNumbers[selectedIndex + 1];

            const expanded = uniq([...state.expanded, selected]);

            return { ...state, expanded, selected };
        }
        case 'SELECT_PREVIOUS_FRAME': {
            const selectableFrameNumbers = addFrameNumbers(state.frames)
                .filter(frame => frame.relative_file !== 'unknown')
                .map(frame => frame.frame_number);

            const selectedIndex = selectableFrameNumbers.indexOf(state.selected);

            const selected =
                selectedIndex === 0
                    ? selectableFrameNumbers[selectableFrameNumbers.length - 1]
                    : selectableFrameNumbers[selectedIndex - 1];

            const expanded = uniq([...state.expanded, selected]);

            return { ...state, expanded, selected };
        }
        default: {
            return state;
        }
    }
}
