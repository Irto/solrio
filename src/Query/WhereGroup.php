<?php namespace Irto\Solrio\Query;

/**
 * Class WhereGroup
 * 
 * @package Irto\Solrio\Query
 */
class WhereGroup 
{
    /**
     * Raw condition query
     * 
     * @var string
     */
    protected $raw = '(';

    /**
     * Return the query condition
     * 
     * @return string
     */
    public function getValue()
    {
        return trim($this->raw) . ')';
    }

    /**
     * Magic to string method
     * 
     * @return string
     */
    public function __toString()
    {
        return $this->getValue();
    }

    /**
     * Add a new searchable string to the condition
     * 
     * @param SearchableString $term
     * @param string $operator
     * 
     * @return self
     */
    public function add(SearchableString $term, $operator = 'OR')
    {
        if (strlen($this->raw) > 1) {
            $this->raw .= " {$operator} ";
        }

        $this->raw .= $term->getValue();

        return $this;
    }

    /**
     * Add new term or phrase to condition with the $options
     * 
     * Available options can be found on {@link Irto\Solrio\Query\SearchableString}
     * 
     * @param string $value
     * @param array $options
     * @param string $operator
     * 
     * @return self
     */
    public function addTerm($value, array $options, $operator = 'OR')
    {
        $terms = new SearchableString($value);

        return $this->add(
            $terms->processOptions($options),
            $operator
        );
    }
}