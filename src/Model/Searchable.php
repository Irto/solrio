<?php namespace Irto\Solrio\Model;

/**
 * Interface Searchable
 * 
 * @package Irto\Solrio\Model
 */
interface Searchable
{
    /**
     * Is the model available for search indexing?
     *
     * @return boolean
     */
    public function isSearchable();
}