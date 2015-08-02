<?php
return [
    /*
     |--------------------------------------------------------------------------
     | The configurations of search index.
     |--------------------------------------------------------------------------
     |
     | The "models" is the list of the descriptions for models. Each description
     | must contains class of model and fields available for search indexing.
     |
     | For example, model's description can be like this:
     |
     |      namespace\ModelClass::class => [
     |          'fields' => [
     |              'name', 'description', // Fields for indexing.
     |          ]
     |      ]
     |
     */
    'index' => [
        'models' => [
            // Add models descriptions here.
        ],
    ],

    /*
     |--------------------------------------------------------------------------
     | The solarium client configurations 
     |--------------------------------------------------------------------------
     |
     | The main configuration for Solarium client.
     |
     | You can see more about adapters in http://wiki.solarium-project.org/index.php/V3:Client_and_adapters
     |
     | Update endpoint configuration with your installation options, example:
     |
     |      'localhost' => array(
     |          'host' => '127.0.0.1',  
     |          'port' => 8983, // default port
     |          'path' => '/solr/',
     |      )
     |
     */
    'client' => [

        'adapter' => 'Solarium\Core\Client\Adapter\Curl',

        'endpoint' => [
            'localhost' => []
        ]
    ]
];