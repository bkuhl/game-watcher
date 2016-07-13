<?php

return [

    \App\Games\Factorio\Factorio::NAME => [

        'github' => [
            'namespace'     => env('FACTORIO_GITHUB_NAMESPACE'),
            'repository'    => env('FACTORIO_GITHUB_REPOSITORY')
        ],

        // either a URL to a path
        'releases-source' => env('FACTORIO_UPDATE_SOURCE', 'https://www.factorio.com/updater/get-available-versions?apiVersion=2')
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
