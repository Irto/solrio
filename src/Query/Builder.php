<?php namespace Irto\Solrio\Query;

use Solarium;

/**
 * Class Builder
 * 
 * @package Irto\Solrio\Query
 */
class Builder
{
    /**
     * Solarium Client
     * 
     * @var \Solarium\Client $client
     */
    protected $client = null;

    /**
     * Solarium Query
     * 
     * @var \Solarium\
     */
    protected $query = null;

    /**
     * Constructor
     * 
     * @param \Solarium\Client $client
     * 
     * @return Irto\Solrio\Query\Builder
     */
    public function __construct(Solarium\Client $client, $query = null)
    {
        $this->client = $client;
        $this->query = $client->createSelect();

        if (!empty($query)) {
            $this->setQuery($query);
        }
    }

    /**
     * Execute the query and returns the result
     * 
     * @return int
     */
    public function count()
    {
        return $this->get(array())->getNumFound();
    }

    /**
     * Execute the query and returns the result
     * 
     * @param array $fields [null] set fields to fetch (null don't change current selection)
     * 
     * @return \Solarium\QueryType\Select\Result\Result
     */
    public function get(array $fields = null)
    {
        if ($fields !== null) {
            $this->query->setFields($fields);
        }

        return $this->client->select($this->query);
    }

    /**
     * Magic function __call, forward unavailable methods to Solarium query
     * 
     * @param string $method
     * @param array $parameters
     */
    public function __call($method, array $parameters)
    {
        $return = call_user_func_array([$this->query, $method], $parameters);

        if ($return == $this->query) {
            return $this;
        }

        return $return;
    }
}