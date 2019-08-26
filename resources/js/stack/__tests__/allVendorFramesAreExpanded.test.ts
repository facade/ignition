import createFlareErrorFrame from './__helpers__/createFlareErrorFrame';
import allVendorFramesAreExpanded from '../selectors/allVendorFramesAreExpanded';

describe('allVendorFramesAreExpanded', () => {
    test('it can determine that all vendor frames are expanded', () => {
        const state = {
            frames: [
                createFlareErrorFrame({ relative_file: 'a.php' }),
                createFlareErrorFrame({ relative_file: 'vendor/a.php' }),
                createFlareErrorFrame({ relative_file: 'vendor/b.php' }),
            ],
            expanded: [3, 2, 1],
            selected: 3,
        };

        expect(allVendorFramesAreExpanded(state)).toBe(true);
    });

    test('it can determine that not all vendor frames are expanded', () => {
        const state = {
            frames: [
                createFlareErrorFrame({ relative_file: 'a.php' }),
                createFlareErrorFrame({ relative_file: 'vendor/a.php' }),
                createFlareErrorFrame({ relative_file: 'vendor/b.php' }),
            ],
            expanded: [3, 1],
            selected: 3,
        };

        expect(allVendorFramesAreExpanded(state)).toBe(false);
    });
});
