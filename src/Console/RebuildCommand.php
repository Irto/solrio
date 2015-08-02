<?php namespace Irto\Solrio\Console;

use App;
use Config;
use Illuminate\Console\Command;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Output\NullOutput;

use Irto\Solrio\Search;

/**
 * Search index rebuild command
 * 
 * @package Irto\Solrio\Console
 */
class RebuildCommand extends Command
{
    protected $name = 'search:rebuild';

    protected $description = 'Rebuild the search index';

    protected $search = null;

    public function fire()
    {
        if (!$this->option('verbose')) {
            $this->output = new NullOutput;
        }

        $this->call('search:clear');

        /** @var Search $search */
        $this->search = App::make('search');
        $modelRepositories = array_keys($this->search->config('.index.models'));

        if (count($modelRepositories) > 0) {

            $this->rebuildRepositories($modelRepositories);

            $this->info(PHP_EOL . 'Operation is fully complete!');
        } else {
            $this->error('No models found in config file..');
        }
    }

    /**
     * Rebuilde a lista of repositories
     * 
     * @param array $modelRepositories
     * 
     * @return void
     */
    public function rebuildRepositories(array $modelRepositories)
    {
        foreach ($modelRepositories as $modelRepository) {
            $this->info('Creating index for model: "' . $modelRepository . '"');

            $modelRepository = new $modelRepository;
            $models = $modelRepository->all()->all();
            $count = count($models);

            if ($count > 0) {
                $this->rebuildModels($models);
            } else {
                $this->comment(' No available models found. ');
            }
        }

        App::make('Solarium\Client')->update(
            App::make('Solarium\QueryType\Update\Query\Query')
        );
    }

    /**
     * Rebuild a list of models
     * 
     * @param array $models
     * 
     * @return void
     */
    public function rebuildModels(array $models)
    {
        $count = count($models);

        /** @var ProgressBar $progress */
        $progress = new ProgressBar($this->getOutput(), ++$count);

        foreach ($models as $model) {
            $this->search->update($model);
            $progress->advance();
        }

        App::make('Solarium\QueryType\Update\Query\Query')->addCommit();

        $progress->finish();
    }
}