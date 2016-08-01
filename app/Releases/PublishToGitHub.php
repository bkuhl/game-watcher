<?php

namespace App\Releases;

use App\Games\PublishesVersions;
use App\GitHub;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class PublishToGitHub implements ShouldQueue
{
    use InteractsWithQueue;

    /** @var PublishesVersions */
    private $game;

    /** @var Version */
    private $version;

    public function __construct(PublishesVersions $game, Version $version) {
        $this->game = $game;
        $this->version = $version;
    }

    public function handle()
    {
        /** @var GitHub $github */
        $github = app(GitHub::class, [
            $this->game->name()
        ]);
        
        $github->release($this->version);
    }
}