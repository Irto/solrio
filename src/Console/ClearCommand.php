<?php  namespace Irto\Solrio\Console;

use App;
use Illuminate\Console\Command;
use Symfony\Component\Console\Output\NullOutput;

/**
 * Rebuild command
 * 
 * @package Irto\Solrio\Console
 */
class ClearCommand extends Command
{
    protected $name = 'search:clear';

    protected $description = 'Clear the search index storage';

    public function fire()
    {
        $search = App::make('search');
        $update = App::make('Solarium\QueryType\Update\Query\Query');

        $update->addDeleteQuery('*');

        App::make('Solarium\Client')->update(
            $update->addCommit()
        );
    }
}