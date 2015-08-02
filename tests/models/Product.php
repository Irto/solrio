<?php namespace tests\models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

use Irto\Solrio\Model\Searchable;
use Irto\Solrio\Model\SearchTrait;

/**
 * Class Product
 * 
 * @property string $name
 * @property string $description
 * @property boolean $publish
 * 
 * @package tests\models
 */
class Product extends Model implements Searchable
{
    use SearchTrait;
    
    /**
     * @inheritdoc
     */
    public function isSearchable()
    {
        return $this->publish;
    }
}