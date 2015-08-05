<?php namespace Irto\Solrio;

use App;
use Config;
use Solarium;
use Solarium\QueryType\Update\Query\Query as UpdateQuery;
use Illuminate\Database\Eloquent\Model;

use Irto\Solrio\Model\Searchable;

/**
 * Class Search
 *
 * @package Irto\Solrio
 */
class Search 
{
    /**
     * Return configurations for Solrio
     * 
     * @param string $key to get from configuration is dot notation
     * 
     * @return array
     */
    public function config($key = '')
    {
        return Config::get('solrio' . $key);
    }

    /**
     * Return configurations for model with name $class
     * 
     * @param string|object $class
     * @param string $key to get from model configuration is dot notation
     * 
     * @return array
     */
    public function modelConfig($class, $key = '')
    {
        if (is_object($class)) {
            $class = get_class($class);
        }

        return array_get(
            $this->config('.index.models'), 
            $class . $key
        );
    }

    /**
     * Get configured searchable fields from model with it's values
     * 
     * @param \Illuminate\Database\Eloquent\Model $model
     * 
     * @return array
     */
    public function getSearchableFields(Model $model)
    {
        $fields = $this->modelConfig($model, '.fields');
        $fields[] = $model->getKeyName();

        return array_only(
            $model->attributesToArray(), 
            $fields
        );
    }

    /**
     * Creates a new query builder 
     *
     * @param string $query
     * 
     * @return Irto\Solrio\Builder
     */
    public function newQuery($query)
    {
        return App::make('Irto\Solrio\Query\Builder', compact('query'));
    }

    /**
     * Verify if model data can be indexed
     * 
     * @param \Illuminate\Database\Eloquent\Model $model
     * 
     * @return bool
     */
    public function isSearchable(Model $model)
    {
        if ($model instanceof Searchable) {
            return $model->isSearchable();
        }

        return true;
    }

    /**
     * Update document in index for model (if it's {@link Searchable})
     *
     * @param \Illuminate\Database\Eloquent\Model $model
     * 
     * @return self
     */
    public function update(Model $model)
    {
        if (!$this->isSearchable($model)) {
            return $this;
        }

        if ($model->exists) {
            $this->delete($model);
        }
        
        $fields = $this->getSearchableFields($model);

        return App::call([$this, 'addDocument'], compact('fields'));
    }

    /**
     * Add delete command to main update query
     * 
     * @param \Solarium\QueryType\Update\Query\Query $query
     * @param array $fields
     * 
     * @return self
     */
    public function addDocument(UpdateQuery $query, array $fields)
    {
        $document = $query->createDocument($fields);

        $query->addDocument($document);

        return $this;
    }

    /**
     * Delete document for model from index.
     * 
     * @param \Illuminate\Database\Eloquent\Model $model
     * 
     * @return self
     */
    public function delete(Model $model)
    {
        $id = $model->getKey();

        return App::call([$this, 'addDelete'], compact('id'));
    }

    /**
     * Add delete command to main update query
     * 
     * @param \Solarium\QueryType\Update\Query\Query $query
     * @param int $id
     * 
     * @return self
     */
    public function addDelete(UpdateQuery $query, $id)
    {
        $query->addDeleteById($id);

        return $this;
    }
}