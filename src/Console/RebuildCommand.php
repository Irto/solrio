<?php namespace Irto\Solrio\Console;

use App;
use Config;
use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\Model;
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
     * Rebuild a list of repositories
     * 
     * @param array $modelRepositories
     * 
     * @return void
     */
    public function rebuildRepositories(array $modelRepositories)
    {
        foreach ($modelRepositories as $modelRepository) {
            $this->info('Creating index for model: "' . $modelRepository . '"');

            $this->rebuildRepository(new $modelRepository);
        }

        $updateQuery = App::make('Solarium\QueryType\Update\Query\Query');
        $updateQuery->addCommit();

        App::make('Solarium\Client')->update($updateQuery);
    }

    /**
     * Rebuild a specific repository
     * 
     * @param \Illuminate\Database\Eloquent\Model $modelRepository
     * 
     * @return void
     */
    public function rebuildRepository(Model $modelRepository)
    {
        $count = $modelRepository->count();

        if ($count < 1) {
            return $this->comment(' No available models found.');
        }

        $progress = new ProgressBar($this->getOutput(), ++$count);

        $modelRepository->chunk(200, function ($models) use ($progress) {
            $this->rebuildModels($models->all(), $progress);
        });

        $progress->finish();
    }

    /**
     * Rebuild a list of models
     * 
     * @param array $models
     * @param ProgressBar $progress
     * 
     * @return void
     */
    public function rebuildModels(array $models, ProgressBar $progress)
    {
        $count = count($models);

        foreach ($models as $model) {
            $this->search->update($model);
            $progress->advance();
        }
    }
}