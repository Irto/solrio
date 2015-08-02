<?php namespace tests\functional;

use Config;
use Illuminate\Support\Facades\Artisan;

use tests\TestCase;

/**
 * Class BaseTestCase
 * 
 * @package tests\functional
 */
abstract class BaseTestCase extends TestCase
{
    public function setUp()
    {
        parent::setUp();
        $this->configure();
    }

    protected function configure()
    {
        Config::set(
            'solrio.index.models',
            [
                'tests\models\Product' => [
                    'fields' => [
                        'name',
                        'description'
                    ],
                    'optional_attributes' => true
                ]
            ]
        );

        Config::set(
            'solrio.client', 
            [

                'endpoint' => [
                    'localhost' => [
                        'path' => '/solr/gamergrade'
                    ]
                ]
            ]
        );

        // Call migrations specific to our tests, e.g. to seed the db.
        Artisan::call('migrate', ['--database' => 'testbench', '--path' => '../tests/migrations']);
        
        // Call rebuild search index.
        Artisan::call('search:rebuild');
    }
}