<?php namespace tests\functional;

use App;
use Config;
use Search;

class QueryBuilderTest extends BaseTestCase
{
    protected function configure()
    {
        parent::configure();
    }

    public function testRun()
    {
        $builder = App::make('Irto\Solrio\Query\Builder', ['query' => 'description:*big analog*~1']);

        $result = $builder->run();

        $this->assertInstanceOf('Solarium\Core\Query\Result\ResultInterface', $result);
    }

    public function testSearchResults()
    {
        $builder = App::make('Irto\Solrio\Query\Builder', ['query' => 'description:*big analog*~1']);

        $result = $builder->get();

        $this->assertCount(2, $result);
    }

    public function testGetResultFields()
    {
        $builder = App::make('Irto\Solrio\Query\Builder', ['query' => 'description:*big analog*~1']);

        $result = $builder->get(['name']);
        
        $this->assertEquals([
            ['name' => ["big analog clock"]],
            ['name' => ["simple analog clock"]]
        ], $result->toArray());
    }

    public function testCount()
    {
        $builder = App::make('Irto\Solrio\Query\Builder', ['query' => '"small"']);

        $result = $builder->count();

        $this->assertEquals(3, $result);
    }
}