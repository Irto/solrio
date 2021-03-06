# solrio
[![Latest Stable Version](https://poser.pugx.org/irto/solrio/v/stable)](https://packagist.org/packages/irto/solrio)
[![Latest Unstable Version](https://poser.pugx.org/irto/solrio/v/unstable)](https://packagist.org/packages/irto/solrio)
[![License](https://poser.pugx.org/irto/solrio/license)](https://packagist.org/packages/irto/solrio)

Laravel 5.1 package for full-text search over Eloquent models based on Solarium, inspired by [Laravel Lucene Search](https://github.com/nqxcode/laravel-lucene-search)

## Installation

Require this package in your composer.json and run composer update:

```php
    {
        "require": {
            "irto/solrio": "0.*"
        }
    }
```

After updating composer, add the ServiceProvider to the providers array in app/config/app.php

```php
    'providers' => [
        Irto\Solrio\ServiceProvider::class,
    ],
```

If you want to use the facade to search, add this to your facades in app/config/app.php:

```php
    'aliases' => [
        'Search' => Irto\Solrio\Facade::class,
    ],
```

##Configuration

Publish the config file into your project by running:

```php
    php artisan vendor:publish --provider="Irto\Solrio\ServiceProvider"
```

In published config file add descriptions for models which need to be indexed, for example:

```php
    'index' => [
        
        'models' => [

            // ...

            namespace\FirstModel::class => [
                'fields' => [
                    'name', 'full_description', // Fields for indexing.
                ]
            ],

            namespace\SecondModel::class => [
                'fields' => [
                    'name', 'short_description', // Fields for indexing.
                ]
            ],

            // ...

        ],
    ],
```

##Usage

###Artisan commands

####Build/Rebuild search index

For building of search index run:

```
    php artisan search:rebuild
```

####Clear search index

For clearing of search index run:

```
    php artisan search:clear
```

###Partial updating of search index

For register of necessary events (save/update/delete) use Irto\Solrio\Model\SearchTrait in target model:

```php
    use Illuminate\Database\Eloquent\Model;

    use Irto\Solrio\Model\Searchable;
    use Irto\Solrio\Model\SearchTrait;

    class Dummy extends Model implements Searchable // use of Searchable is optional, without this will be always available to search
    {
        use SearchTrait;

        /**
         * Is the model available for searching?
         */
        public function isSearchable()
        {
            return $this->publish;
        }
    }
```

You can also do it manually, how on a queued listener:

```php
    use Illuminate\Queue\InteractsWithQueue;
    use Illuminate\Contracts\Queue\ShouldQueue;

    use Search; // if alias is configured

    class DummyUpdatedListener extends ShouldQueue
    {
        use InteractsWithQueue;

        public function handle($event)
        {
            $model = $event->getModel();

            Search::update($model); // you can use 'App::offsetGet('search')->update($model);' instead
        }
    }
```