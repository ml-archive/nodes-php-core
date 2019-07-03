<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Project name
    |--------------------------------------------------------------------------
    |
    | Name of project. This should be the same as when the project was created
    | via the "nodes.backend-create" script. If you forgot, look it up on our Gitlab.
    |
    */
    'name' => 'DummyProjectName',

    /*
    |--------------------------------------------------------------------------
    | Project namespace
    |--------------------------------------------------------------------------
    |
    | Namespace of the project. This will required when using our
    | Nodes generators. E.g. to generate controllers, models, routes etc.
    |
    */
    'namespace' => 'DummyNamespace',
    
      /*
    |--------------------------------------------------------------------------
    | Load Browscap
    |--------------------------------------------------------------------------
    | 
    | Should only be activated if user agent is parsed and used
    |
    */
    'browscap' => false
];
