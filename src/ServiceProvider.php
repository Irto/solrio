<?php namespace Irto\Solrio;

use App;
use Config;
use Solarium;

use Irto\Solrio\Search;

class ServiceProvider extends \Illuminate\Support\ServiceProvider
{
    /**
     * Indicates if loading of the provider is deferred.
     *
     * @var bool
     */
    protected $defer = true;

    /**
     * Bootstrap the application events.
     *
     * @return void
     */
    public function boot()
    {
        $this->mergeConfigFrom(__DIR__ . '/../config/config.php', 'solrio');
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->publishes([
            __DIR__ . '/../config/config.php' => config_path('solrio.php'),
        ]);

        $this->app->singleton('Solarium\Client', function ($app) {
            $config = Config::get('solrio.client');

            return new Solarium\Client($config);
        });

        $this->app->singleton('Solarium\QueryType\Update\Query\Query', function ($app) {
            $client = $app->make('Solarium\Client');
            $update = $client->createUpdate();

            return $update;
        });
        
        $this->app->singleton('search', 'Irto\Solrio\Search');
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return [
            'search',
            'Solarium\Client',
            'Solarium\QueryType\Update\Query\Query',
        ];
    }
}