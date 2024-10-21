<?php
return [

/*
 |--------------------------------------------------------------------------
 | Debugbar enabled
 |--------------------------------------------------------------------------
 |
 | You can manually enable or disable the Debugbar here.
 | This setting can also be controlled via your .env file, using DEBUGBAR_ENABLED.
 | You should not use the debugbar in production, so ensure that DEBUGBAR_ENABLED
 | is false in production.
 |
 */

'enabled' => env('DEBUGBAR_ENABLED', false),

/*
 |--------------------------------------------------------------------------
 | Capture Ajax Requests
 |--------------------------------------------------------------------------
 |
 | The Debugbar can capture and display Ajax requests. If you don't want this,
 | you can disable it.
 |
 */

'capture_ajax' => true,

/*
 |--------------------------------------------------------------------------
 | DataCollectors
 |--------------------------------------------------------------------------
 |
 | Here you can enable/disable DataCollectors
 |
 */

'collectors' => [
    'phpinfo' => true,
    'messages' => true,
    'time' => true,
    'memory' => true,
    'exceptions' => true,
    'log' => true,
    'db' => true,
    'views' => true,
    'route' => true,
    'auth' => false,
    'gate' => true,
    'session' => true,
    'request' => true,
    'events' => true,
    'default_request' => true,
    'symfony_request' => true,
    'mail' => true,
    'logs' => true,
    'files' => false,
    'config' => false,
    'cache' => true,
    'models' => true,
],

/*
 |--------------------------------------------------------------------------
 | Inject Debugbar in Response
 |--------------------------------------------------------------------------
 |
 | The Debugbar can inject itself into the response body automatically.
 |
 */

'inject' => true,

// Other configuration options...
];
