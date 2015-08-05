<?php namespace Irto\Solrio\Query;

use Solarium;
use Illuminate\Support\Collection;
use Solarium\Core\Query\Result\ResultInterface;

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
     * @var \Solarium\QueryType\Select\Query\Query
     */
    protected $query = null;

    /**
     * Solarium Query Text
     * 
     * @var string
     */
    protected $rawQuery = null;

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
        $this->rawQuery = $query;
    }

    /**
     * Execute the query and returns the result
     * 
     * @return int
     */
    public function count()
    {
        return $this->prepare([])->run()->getNumFound();
    }

    /**
     * Execute the query and returns the result
     * 
     * @param array $fields [null] set fields to fetch (null don't change current selection)
     * 
     * @return \Illuminate\Support\Collection
     */
    public function get(array $fields = null)
    {
        $this->prepare($fields);

        $result = $this->run();

        return $this->prepareResultset($result);
    }

    /**
     * Execute query and return the resultset
     * 
     * @return \Solarium\Core\Query\Result\ResultInterface
     */
    public function run()
    {
        return $this->client->select($this->query);
    }

    /**
     * Prepare query for to run
     * 
     * @param array $fields to be selected
     * 
     * @return self
     */
    public function prepare(array $fields = null)
    {
        if ($fields !== null) {
            $this->query->setFields($fields);
        }

        $this->query->setQuery($this->rawQuery);

        return $this;
    }

    /**
     * Prepare solarium resultset to return as Illuminate Collection
     * 
     * @param \Solarium\Core\Query\Result\ResultInterface $resultset
     * 
     * @return \Illuminate\Support\Collection
     */
    public function prepareResultset(ResultInterface $resultset)
    {
        $data = $resultset->getData();

        return new Collection($data['response']['docs']);
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