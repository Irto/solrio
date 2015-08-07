<?php namespace Irto\Solrio\Query;

use Solarium\Core\Query\Helper;
use Illuminate\Support\Traits\Macroable;

/**
 * Class SearchableString
 * 
 * @package Irto\Solrio\Query
 */
class SearchableString 
{
    use Macroable;

    /**
     * Original string to be searched
     * 
     * @var string
     */
    protected $original = null;

    /**
     * Processed string
     * 
     * @var string
     */
    protected $processed = null;

    /**
     * Constructor
     * 
     * @param string $string to be wrapped
     * 
     * @return \Irto\Solrio\Query\SearchableString
     */
    public function __construct($string)
    {
        $this->processed = $this->original = $string;
    }

    /**
     * Return solarium query helper
     * 
     * @return \Solarium\Core\Query\Helper
     */
    protected function getHelper()
    {
        return new Helper();
    }

    /**
     * return processed value
     * 
     * @return string
     */
    public function getValue()
    {
        return $this->processed;
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
     * Process options to the processed
     * 
     * @param array $options
     * 
     * @return self
     */
    public function processOptions(array $options)
    {
        foreach ($options as $method => $param) {
            $this->processed = call_user_func([$this, $method], $param);
        }

        return $this;
    }

    /**
     * 
     * 
     * @param int|bool $param
     * 
     * @return string
     */
    public function fuzzy($param)
    {
        if ($param === true) {
            return "{$this->processed}~";
        } else if ($param > 0) {
            return "{$this->processed}~{$param}";
        }

        return $this->processed;
    }

    /**
     * 
     * 
     * @param bool $param
     * 
     * @return string
     */
    public function phrase($param)
    {
        $helper = $this->getHelper();

        if ($param == true) {
            return $helper->escapePhrase($this->processed);
        }

        return $helper->escapeTerm($this->processed);
    }

    /**
     * 
     * 
     * @param bool $param
     * 
     * @return string
     */
    public function required($param)
    {
        if ($param == true) {
            return "+{$this->processed}";
        }

        return $this->processed;
    }

    /**
     * 
     * 
     * @param bool $param
     * 
     * @return string
     */
    public function prohibited($param)
    {
        if ($param == true) {
            return "-{$this->processed}";
        }
        
        return $this->processed;
    }

    /**
     * 
     * 
     * @param int $param
     * 
     * @return string
     */
    public function proximity($param)
    {
        if ($param > 0) {
            return "{$this->processed}~{$param}";
        }
        
        return $this->processed;
    }

    /**
     * 
     * 
     * @param int $param
     * 
     * @return string
     */
    public function boost($param)
    {
        if ($param > 0) {
            return "{$this->processed}^{$param}";
        }

        return $this->processed;
    }

    /**
     * 
     * 
     * @param bool $param
     * 
     * @return string
     */
    public function wildcardWrap($param)
    {
        if ($param) {
            return "*{$this->processed}*";
        }

        return $this->processed;
    }
}