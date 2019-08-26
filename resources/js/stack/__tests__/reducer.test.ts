import createFlareErrorFrame from './__helpers__/createFlareErrorFrame';
import reducer from '../reducer';

describe('reducer', () => {
    describe('EXPAND_FRAMES', () => {
        const initialState = {
            frames: [
                createFlareErrorFrame({ relative_file: 'a.php' }),
                createFlareErrorFrame({ relative_file: 'vendor/b.php' }),
            ],
            expanded: [2],
            selected: 2,
        };

        test('it can expand a frame', () => {
            const state = reducer(initialState, { type: 'EXPAND_FRAMES', frames: [1] });

            expect(state.expanded).toEqual([2, 1]);
        });
    });

    describe('EXPAND_ALL_VENDOR_FRAMES', () => {
        const initialState = {
            frames: [
                createFlareErrorFrame({ relative_file: 'a.php' }),
                createFlareErrorFrame({ relative_file: 'vendor/b.php' }),
                createFlareErrorFrame({ relative_file: 'vendor/c.php' }),
            ],
            expanded: [3],
            selected: 3,
        };

        test('it can expand all vendor frames', () => {
            const state = reducer(initialState, { type: 'EXPAND_ALL_VENDOR_FRAMES' });

            expect(state.expanded).toEqual([3, 2, 1]);
        });
    });

    describe('COLLAPSE_ALL_VENDOR_FRAMES', () => {
        test('it can collapse all vendor frames', () => {
            const initialState = {
                frames: [
                    createFlareErrorFrame({ relative_file: 'a.php' }),
                    createFlareErrorFrame({ relative_file: 'vendor/b.php' }),
                    createFlareErrorFrame({ relative_file: 'vendor/c.php' }),
                ],
                expanded: [3, 2, 1],
                selected: 3,
            };

            const state = reducer(initialState, { type: 'COLLAPSE_ALL_VENDOR_FRAMES' });

            expect(state.expanded).toEqual([3]);
        });
    });

    describe('SELECT_FRAME', () => {
        test('it can select a frame', () => {
            const initialState = {
                frames: [
                    createFlareErrorFrame({ relative_file: 'a.php' }),
                    createFlareErrorFrame({ relative_file: 'b.php' }),
                ],
                expanded: [2, 1],
                selected: 2,
            };

            const state = reducer(initialState, { type: 'SELECT_FRAME', frame: 1 });

            expect(state.selected).toBe(1);
        });

        test('it expands a selected frame', () => {
            const initialState = {
                frames: [
                    createFlareErrorFrame({ relative_file: 'a.php' }),
                    createFlareErrorFrame({ relative_file: 'vendor/b.php' }),
                ],
                expanded: [2],
                selected: 2,
            };

            const state = reducer(initialState, { type: 'SELECT_FRAME', frame: 1 });

            expect(state.selected).toBe(1);
            expect(state.expanded).toEqual([2, 1]);
        });

        test('it keeps the selected frame if a non existing frame is selected', () => {
            const initialState = {
                frames: [
                    createFlareErrorFrame({ relative_file: 'a.php' }),
                    createFlareErrorFrame({ relative_file: 'b.php' }),
                ],
                expanded: [2, 1],
                selected: 2,
            };

            const state = reducer(initialState, { type: 'SELECT_FRAME', frame: 3 });

            expect(state.selected).toBe(2);
        });
    });

    describe('SELECT_NEXT_FRAME', () => {
        test('it can select the next frame', () => {
            const initialState = {
                frames: [
                    createFlareErrorFrame({ relative_file: 'a.php' }),
                    createFlareErrorFrame({ relative_file: 'b.php' }),
                ],
                expanded: [2, 1],
                selected: 2,
            };

            const state = reducer(initialState, { type: 'SELECT_NEXT_FRAME' });

            expect(state.selected).toBe(1);
        });

        test('it selects the first frame when there is no next frame', () => {
            const initialState = {
                frames: [
                    createFlareErrorFrame({ relative_file: 'a.php' }),
                    createFlareErrorFrame({ relative_file: 'b.php' }),
                ],
                expanded: [2, 1],
                selected: 1,
            };

            const state = reducer(initialState, { type: 'SELECT_NEXT_FRAME' });

            expect(state.selected).toBe(2);
        });

        test('it skips unknown frames', () => {
            const initialState = {
                frames: [
                    createFlareErrorFrame({ relative_file: 'a.php' }),
                    createFlareErrorFrame({ relative_file: 'unknown' }),
                    createFlareErrorFrame({ relative_file: 'b.php' }),
                ],
                expanded: [3, 1],
                selected: 3,
            };

            const state = reducer(initialState, { type: 'SELECT_NEXT_FRAME' });

            expect(state.selected).toBe(1);
        });
    });

    describe('SELECT_PREVIOUS_FRAME', () => {
        test('it can select the previous frame', () => {
            const initialState = {
                frames: [
                    createFlareErrorFrame({ relative_file: 'a.php' }),
                    createFlareErrorFrame({ relative_file: 'b.php' }),
                ],
                expanded: [2, 1],
                selected: 1,
            };

            const state = reducer(initialState, { type: 'SELECT_PREVIOUS_FRAME' });

            expect(state.selected).toBe(2);
        });

        test('it selects the last frame when there is no previous frame', () => {
            const initialState = {
                frames: [
                    createFlareErrorFrame({ relative_file: 'a.php' }),
                    createFlareErrorFrame({ relative_file: 'b.php' }),
                ],
                expanded: [2, 1],
                selected: 2,
            };

            const state = reducer(initialState, { type: 'SELECT_PREVIOUS_FRAME' });

            expect(state.selected).toBe(1);
        });

        test('it skips unknown frames', () => {
            const initialState = {
                frames: [
                    createFlareErrorFrame({ relative_file: 'a.php' }),
                    createFlareErrorFrame({ relative_file: 'unknown' }),
                    createFlareErrorFrame({ relative_file: 'b.php' }),
                ],
                expanded: [3, 1],
                selected: 1,
            };

            const state = reducer(initialState, { type: 'SELECT_PREVIOUS_FRAME' });

            expect(state.selected).toBe(3);
        });
    });
});
