export type State = {
    frames: Array<Frame>;
    selected: number;
    expanded: Array<number>;
};

export type Action =
    | { type: 'EXPAND_FRAMES'; frames: Array<number> }
    | { type: 'EXPAND_ALL_VENDOR_FRAMES' }
    | { type: 'COLLAPSE_ALL_VENDOR_FRAMES' }
    | { type: 'SELECT_FRAME'; frame: number }
    | { type: 'SELECT_NEXT_FRAME' }
    | { type: 'SELECT_PREVIOUS_FRAME' };

export type Frame = {
    id: number;
    class: string;
    method: string;
    code_snippet: { [lineNumber: string]: string };
    file: string;
    relative_file: string;
    line_number: number;
};

export type FrameType = 'application' | 'vendor' | 'unknown';

export type FrameGroup = {
    type: FrameType;
    relative_file: string;
    expanded: boolean;
    frames: Array<Frame & { frame_number: number; selected: boolean }>;
};
