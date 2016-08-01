<?php

namespace App\Releases;

use App\Games\PublishesVersions;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class PublishRelease implements ShouldQueue
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
        $this->game->publish($this->version);
    }
}