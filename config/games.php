<?php

return [

    \App\Games\Factorio\Factorio::NAME => [

        'namespace'     => env('FACTORIO_GITHUB_FORK_NAMESPACE'),

        // this fork will first be updated and a pull request create for the other repository
        'github-fork' => [
            'namespace'     => env('FACTORIO_GITHUB_FORK_NAMESPACE'),
            'repository'    => env('FACTORIO_GITHUB_FORK_REPOSITORY')
        ],

        // repository to update the Dockerfiles when a new game version is detected
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
