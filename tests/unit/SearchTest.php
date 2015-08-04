<?php namespace tests\unit;

use Irto\Solrio\Search;

use tests\models\Product;
use tests\models\DummyModel;
use tests\TestCase;

class SearchTest extends TestCase 
{
    protected $product = null;

    protected $dummy = null;

    public function setUp()
    {
        parent::setUp();

        $this->product = new Product;
        $this->product->id = 1;
        $this->product->name = 'test name';

        $this->dummy = new DummyModel;
        $this->dummy->id = 2;
        $this->dummy->name = 'another test name';
    }

    private function createSearch()
    {
        return new Search();
    }

    public function testIsSearchable()
    {
        $this->product->publish = true;

        $search = $this->createSearch();
        $result = $search->isSearchable($this->product);

        $this->assertTrue($result);
    }

    public function testIsNotSearchable()
    {
        $this->product->publish = false;

        $search = $this->createSearch();
        $result = $search->isSearchable($this->product);

        $this->assertFalse($result);
    }

    public function testWithoutSearchable()
    {
        $search = $this->createSearch();
        $result = $search->isSearchable($this->dummy);

        $this->assertTrue($result);
    }
}