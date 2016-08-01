<?php

namespace App\Games\Factorio;

use App\GitHub;
use App\Games\PublishesVersions;
use App\Releases\Version;
use Illuminate\Support\Collection;

class Factorio implements PublishesVersions
{
    const NAME = 'factorio';

    /** @var ReleaseProvider */
    protected $releaseProvider;

    /** @var GitHub */
    protected $github;

    public function __construct(ReleaseProvider $releaseProvider) {
        $this->releaseProvider = $releaseProvider;
        $this->github = app(GitHub::class, [
            self::name()
        ]);
    }

    /**
     * @return array
     */
    public function unpublishedVersions() : Collection
    {
        /** @var Releases $releases */
        $releases = $this->releaseProvider->releases();

        return $releases->all()->filter(function ($release) {
            return $this->github->hasNotBeenReleased($release);
        });
    }

    public function name() : string
    {
        return self::NAME;
    }
}