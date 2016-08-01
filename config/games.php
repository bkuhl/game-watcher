<?php

return [

    \App\Games\Factorio\Factorio::NAME => [

        // repository to create releases for when new versions are detected
        'github' => [
            'namespace'     => env('FACTORIO_GITHUB_NAMESPACE'),
            'repository'    => env('FACTORIO_GITHUB_REPOSITORY')
        ],

        // either a URL to a path
        'releases-source' => env('FACTORIO_UPDATE_SOURCE', 'https://www.factorio.com/updater/get-available-versions?apiVersion=2'),

        // URL where the server client can be downloaded to generate the sha1 hash
        // {VERSION} will be replaced with a semantic version
        'client-url' => env('FACTORIO_CLIENT_SOURCE', 'https://www.factorio.com/get-download/{VERSION}/headless/linux64')
    ]

    /*
     * This sample configuration block should be copied for any new
     * games that are added
     *
     * Note: use snake_case for game names (i.e. - pay_day for "Pay Day")
     * ------------------------------------------------------
        \App\Games\NewGame\NewGame::NAME => [

            // For each new release of the game, a new "GitHub Release" will be published
            // with that game's version number
            'github' => [
                'namespace'     => '',
                'repository'    => ''
            ],

        ],
     * ------------------------------------------------------
    */

];
