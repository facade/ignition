import { Frame } from '../../types';

export default function createFlareErrorFrame(attributes: Partial<Frame>): Frame {
    return {
        id: 541,
        file:
            '/Users/sebastiandedeyne/Sites/flare.laravel.com/database/faker/ExceptionProvider.php',
        relative_file: 'database/faker/ExceptionProvider.php',
        line_number: 35,
        class: 'ExceptionProvider',
        method: 'exception',
        code_snippet: {
            '35': '        return Stacktrace::createForThrowable($this->exception())->toArray();',
        },
        ...attributes,
    };
}
