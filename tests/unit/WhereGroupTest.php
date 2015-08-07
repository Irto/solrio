<?php namespace tests\unit;

use tests\TestCase;

use Irto\Solrio\Query\WhereGroup;
use Irto\Solrio\Query\SearchableString;

class WhereGroupTest extends TestCase 
{
    public function testAddTerm()
    {
        $term = 'teste';
        $where = new WhereGroup();

        $result = $where->addTerm($term, ['fuzzy' => 0.8])->getValue();

        $fuzzy = '(' . (new SearchableString($term))->fuzzy(0.8) . ')';

        $this->assertEquals($fuzzy, $result);
    }
    public function testAddTermOperator()
    {
        $term1 = 'rael';
        $term2 = 'rocha';
        $where = new WhereGroup();

        $result = $where
            ->addTerm($term1, ['fuzzy' => 0.8])
            ->addTerm($term2, ['boost' => 5], 'AND')
            ->getValue();

        $term1 = (new SearchableString($term1))->fuzzy(0.8);
        $term2 = (new SearchableString($term2))->boost(5);
        $fuzzy = "({$term1} AND {$term2})";

        $this->assertEquals($fuzzy, $result);
    }

    public function testAdd()
    {
        $term = new SearchableString('teste');
        $expected = clone $term;
        $where = new WhereGroup();

        $term->processOptions(['fuzzy' => 0.5]);

        $result = $where->add($term)->getValue();

        $fuzzy = '(' . $expected->fuzzy(0.5) . ')';

        $this->assertEquals($fuzzy, $result);
    }

    public function testAddOperator()
    {
        $term = new SearchableString('teste');
        $termClone = clone $term;
        $expected = $termClone->fuzzy(0.5);
        $where = new WhereGroup();

        $term->processOptions(['fuzzy' => 0.5]);

        $result = $where->add($term)->add($term, 'AND')->getValue();

        $fuzzy = "({$expected} AND {$expected})";

        $this->assertEquals($fuzzy, $result);
    }

    public function testToString()
    {
        $where = new WhereGroup();

        $this->assertEquals('()', $where->__toString());
    }

    public function testGetValue()
    {
        $where = new WhereGroup();

        $this->assertEquals('()', $where->getValue());
    }
}