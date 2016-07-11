<?php

namespace App\Games\Factorio;

class ReleaseProvider
{
    /** @var Releases */
    private $releases;

    public function __construct(Releases $releases)
    {
        $this->releases = $releases;
    }

    public function releases() : Releases
    {
        $response = file_get_contents(config('games.factorio.releases-source'));
        $releases = json_decode($response, $associativeArray = true);

        return $this->releases->classifyReleases($releases['core-linux_headless64']);
    }
}