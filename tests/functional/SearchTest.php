<?php namespace tests\functional;

use Config;
use Search;

class SearchTest extends BaseTestCase
{
    protected function configure()
    {
        // Test search without analyser filters and stopwords.
        Config::set('laravel-lucene-search.analyzer.filters', []);
        Config::set('laravel-lucene-search.analyzer.stopwords', []);

        parent::configure();
    }

    public function testSearchQueryChain()
    {
        $query = Search::newQuery('"small"');
        $this->assertEquals(3, $query->count());

        $query = Search::newQuery('"simple clock"');
        $this->assertEquals(0, $query->count());

        $query = Search::newQuery('"simple clock"~1');
        $this->assertEquals(1, $query->count());

        $query = Search::newQuery('description:*big analog*~1');
        $this->assertEquals(2, $query->count());

        $query = Search::newQuery('"simple clock"~1');
        $this->assertEquals(1, $query->count());

        $query = Search::newQuery('name:*clock*');
        $this->assertEquals(3, $query->count());
    }
}