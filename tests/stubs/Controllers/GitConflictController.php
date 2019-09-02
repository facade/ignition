<?php

namespace Facade\Ignition\Tests\stubs\Controllers;

/**
 * This has an intentional Git conflict
 */
class GitConflictController
{
    public function __invoke()
    {
<<<<<<< HEAD

        return view('articles.index.thingy', [
            'someVariableName' => 'hello',
            'someOtherVariable' => 'thingy123',
=======
            'someOtherVariable' => 'something',
>>>>>>> another
    }
}
