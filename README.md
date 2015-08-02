# Under Development

# solrio
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