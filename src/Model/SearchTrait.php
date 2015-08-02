<?php namespace Irto\Solrio\Model;

use App;

/**
 * Trait Search
 * 
 * @package Irto\Solrio\Model
 */
trait SearchTrait
{
    /**
     * Set event handlers for updating of search index.
     */
    public static function bootSearchTrait()
    {
        self::saved(
            function ($model) {
                App::offsetGet('search')->update($model);
            }
        );
        
        self::deleting(
            function ($model) {
                App::offsetGet('search')->delete($model);
            }
        );
    }
}