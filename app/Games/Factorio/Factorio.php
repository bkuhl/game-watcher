<?php

namespace App\Games\Factorio;

use App\GitHub;
use App\Games\PublishesVersions;
use App\Releases\Version;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Collection;

class Factorio implements PublishesVersions
{
    const NAME = 'factorio';

    /** @var ReleaseProvider */
    protected $releaseProvider;

    /** @var GitHub */
    protected $github;

    /** @var Filesystem */
    protected $filesystem;

    public function __construct(ReleaseProvider $releaseProvider, Filesystem $filesystem) {
        $this->releaseProvider = $releaseProvider;
        $this->github = app(GitHub::class, [
            self::name()
        ]);
        $this->filesystem = $filesystem;
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

    public function sha1(Version $version) : string
    {
        $clientUrl = str_replace('{VERSION}', $version->version(), config('games.'.self::name().'.client-url'));
        $tmp = storage_path('app/'.str_slug(self::name()).'_'.$version->patchTag().'_'.uniqid());

        $this->filesystem->copy($clientUrl, $tmp);

        $sha1 = sha1_file($tmp);

        $this->filesystem->delete($tmp);

        return $sha1;
    }

    public function name() : string
    {
        return self::NAME;
    }
}