import createFlareErrorFrame from './__helpers__/createFlareErrorFrame';
import createFrameGroups from '../selectors/createFrameGroups';

describe('createFrameGroups', () => {
    let state = {
        frames: [
            createFlareErrorFrame({ relative_file: 'a.php' }),
            createFlareErrorFrame({ relative_file: 'a.php' }),
            createFlareErrorFrame({ relative_file: 'b.php' }),
        ],
        expanded: [3, 2, 1],
        selected: 3,
    };

    let frameGroups = createFrameGroups(state);

    test('it creates list items', () => {
        expect(frameGroups).toHaveLength(2);

        expect(frameGroups[0].type).toBe('application');
        expect(frameGroups[0].relative_file).toBe('a.php');
        expect(frameGroups[0].frames).toHaveLength(2);

        expect(frameGroups[1].type).toBe('application');
        expect(frameGroups[1].relative_file).toBe('b.php');
        expect(frameGroups[1].frames).toHaveLength(1);
    });

    test('it adds frame numbers', () => {
        expect(frameGroups[0].frames[0].frame_number).toBe(3);
        expect(frameGroups[0].frames[1].frame_number).toBe(2);
        expect(frameGroups[1].frames[0].frame_number).toBe(1);
    });

    test('it expands frame groups', () => {
        expect(frameGroups[0].expanded).toBe(true);
        expect(frameGroups[1].expanded).toBe(true);
    });

    test('it selects frames', () => {
        expect(frameGroups[0].frames[0].selected).toBe(true);
        expect(frameGroups[0].frames[1].selected).toBe(false);
        expect(frameGroups[1].frames[0].selected).toBe(false);
    });

    test('it collapses successive vendor frames', () => {
        state = {
            frames: [
                createFlareErrorFrame({ relative_file: 'a.php' }),
                createFlareErrorFrame({ relative_file: 'vendor/b.php' }),
                createFlareErrorFrame({ relative_file: 'vendor/c.php' }),
                createFlareErrorFrame({ relative_file: 'd.php' }),
            ],
            expanded: [4, 1],
            selected: 4,
        };

        frameGroups = createFrameGroups(state);

        expect(frameGroups).toHaveLength(3);

        expect(frameGroups[0].type).toBe('application');
        expect(frameGroups[0].relative_file).toBe('a.php');
        expect(frameGroups[0].frames).toHaveLength(1);
        expect(frameGroups[0].expanded).toBe(true);

        expect(frameGroups[1].type).toBe('vendor');
        expect(frameGroups[1].relative_file).toBe('vendor/b.php');
        expect(frameGroups[1].frames).toHaveLength(2);
        expect(frameGroups[1].expanded).toBe(false);

        expect(frameGroups[2].type).toBe('application');
        expect(frameGroups[2].relative_file).toBe('d.php');
        expect(frameGroups[2].frames).toHaveLength(1);
        expect(frameGroups[2].expanded).toBe(true);
    });

    test('it collapses successive vendor frames', () => {
        state = {
            frames: [
                createFlareErrorFrame({ relative_file: 'a.php' }),
                createFlareErrorFrame({ relative_file: 'unknown' }),
                createFlareErrorFrame({ relative_file: 'unknown' }),
                createFlareErrorFrame({ relative_file: 'd.php' }),
            ],
            expanded: [4, 1],
            selected: 4,
        };

        frameGroups = createFrameGroups(state);

        expect(frameGroups).toHaveLength(3);

        expect(frameGroups[0].type).toBe('application');
        expect(frameGroups[0].relative_file).toBe('a.php');
        expect(frameGroups[0].frames).toHaveLength(1);
        expect(frameGroups[0].expanded).toBe(true);

        expect(frameGroups[1].type).toBe('unknown');
        expect(frameGroups[1].relative_file).toBe('unknown');
        expect(frameGroups[1].frames).toHaveLength(2);
        expect(frameGroups[1].expanded).toBe(false);

        expect(frameGroups[2].type).toBe('application');
        expect(frameGroups[2].relative_file).toBe('d.php');
        expect(frameGroups[2].frames).toHaveLength(1);
        expect(frameGroups[2].expanded).toBe(true);
    });
});
