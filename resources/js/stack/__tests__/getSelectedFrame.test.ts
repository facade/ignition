import createFlareErrorFrame from './__helpers__/createFlareErrorFrame';
import getSelectedFrame from '../selectors/getSelectedFrame';

describe('getSelectedFrame', () => {
    test('it can get the selected frame', () => {
        const state = {
            frames: [
                createFlareErrorFrame({ relative_file: 'a.php' }),
                createFlareErrorFrame({ relative_file: 'b.php' }),
            ],
            expanded: [2, 1],
            selected: 2,
        };

        expect(getSelectedFrame(state).relative_file).toBe('a.php');
    });
});
