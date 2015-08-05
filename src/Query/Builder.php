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
     * Add a clause to be matched on query
     * 
     * @param string $field to be searched
     * @param string $value to be matched
     * @param array $options you can se defaults at {@link Irto\Solrio\Query\Builder::setDefaultOptions()}
     * @param string $operator ['AND']
     * 
     * @return self
     */
    public function where($field, $value, array $options = array(), $operator = 'OR')
    {
        $options = $this->setDefaultOptions($options);
        $value = $this->processValue($value, $options);

        if ($field == '*') {
            $this->concatToRaw("{$value}", $operator);
        } else {
            $this->concatToRaw("{$field}:{$value}", $operator);
        }

        return $this;
    }

    /**
     * Concat part of a query to actual full query
     * 
     * @param string $query
     * @param string $operator
     * 
     * @return void
     */
    private function concatToRaw($query, $operator)
    {
        if (!empty($this->rawQuery)) {
            $this->rawQuery .= " {$operator} ";
        }

        $this->rawQuery .= $query;
    }

    /**
     * Process a string value to be searched with $options
     * 
     * @param string $value
     * @param array $options
     * 
     * @return string
     */
    protected function processValue($value, array $options)
    {
        $string = $this->query->getHelper()->escapePhrase($value);

        if ($options['fuzzy'] === true) {
            $string = "{$string}~";
        } else if ($options['fuzzy'] > 0) {
            $p = $options['fuzzy'];
            $string = "{$string}~{$p}";
        }

        if ($options['phrase']) {
            $string = "\"{$string}\"";
        }

        if ($options['required']) {
            $string = "+{$string}";
        }

        if ($options['prohibited']) {
            $string = "-{$string}";
        }

        if ($options['proximity'] > 0) {
            $p = $options['proximity'];
            $string = "{$string}~{$p}";
        }

        if ($options['boost'] > 0) {
            $b = $options['boost'];
            $string = "{$string}^{$b}";
        }

        return $string;
    }

    /**
     * Return array with default values overridden by $options parameter
     * 
     * Available options defaults:
     * <pre>
     *  $options = [
     *      'required' => true,
     *      'prohibited' => false,
     *      'phrase' => true,
     *      'fuzzy' => null, // 0...1 or true
     *      'boost' => null, // unsigned integer
     *      'proximity' => null, // unsigned integer
     *  ];
     * </pre>
     * 
     * Get look in: {@link http://lucene.apache.org/core/3_6_0/queryparsersyntax.html#Proximity%20Searches}
     * 
     * @param array $options [array()]
     * 
     * @return array
     */
    public function setDefaultOptions(array $options = array()) 
    {
        return [
            'required' => array_get($options, 'required', true),
            'prohibited' => array_get($options, 'prohibited', false),
            'phrase' => array_get($options, 'phrase', true),
            'fuzzy' => array_get($options, 'fuzzy', null),
            'boost' => array_get($options, 'boost', null),
            'proximity' => array_get($options, 'proximity', null),
        ];
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