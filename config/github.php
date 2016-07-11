<?php

return [

    // the user who will be given credit for the taggs/releases
    // that are automatically created by this tool
    'tagger' => [
        'name'  => env('TAGGER_NAME'),
        'email' => env('TAGGER_EMAIL')
    ],

    /*
    |--------------------------------------------------------------------------
    | Default Connection Name
    |--------------------------------------------------------------------------
    |
    | Here you may specify which of the connections below you wish to use as
    | your default connection for all work. Of course, you may use many
    | connections at once using the manager class.
    |
    */

    'default' => 'main',

    /*
    |--------------------------------------------------------------------------
    | GitHub Connections
    |--------------------------------------------------------------------------
    |
    | Here are each of the connections setup for your application. Example
    | configuration has been included, but you may add as many connections as
    | you would like. Note that the 3 supported authentication methods are:
    | "application", "password", and "token".
    |
    */

    'connections' => [

        \App\Games\Factorio\Factorio::NAME => [
            'token'   => env('FACTORIO_GITHUB_TOKEN', env('GITHUB_TOKEN')),
            'method'  => 'token',
            'cache'   => false,
            // 'backoff' => false,
            // 'logging' => Guzzle\Log\MessageFormatter::DEBUG_FORMAT,
            // 'baseUrl' => 'https://api.github.com/',
            // 'version' => 'v3',
        ],

        'alternative' => [
            'clientId'     => 'your-client-id',
            'clientSecret' => 'your-client-secret',
            'method'       => 'application',
            'cache'        => true,
            // 'backoff'      => false,
            // 'logging'      => Guzzle\Log\MessageFormatter::DEBUG_FORMAT,
            // 'baseUrl'      => 'https://api.github.com/',
            // 'version'      => 'v3',
        ],

        'other' => [
            'username' => 'your-username',
            'password' => 'your-password',
            'method'   => 'password',
            'cache'    => true,
            // 'backoff'  => false,
            // 'logging'  => Guzzle\Log\MessageFormatter::DEBUG_FORMAT,
            // 'baseUrl'  => 'https://api.github.com/',
            // 'version'  => 'v3',
        ],

    ],

];
