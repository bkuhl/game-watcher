<?php

namespace App\Games\Factorio;

use App\VCS\Git;
use App\VCS\GitHub;
use App\Games\PublishesVersions;
use App\Releases\Version;
use App\VCS\Repository;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Collection;

class Factorio extends PublishesVersions
{
    const NAME = 'factorio';

    /** @var ReleaseProvider */
    protected $releaseProvider;

    /** @var GitHub */
    protected $github;

    /** @var Filesystem */
    protected $filesystem;

    /** @var Git */
    protected $git;

    public function __construct(ReleaseProvider $releaseProvider, Filesystem $filesystem, Git $git)
    {
        $this->releaseProvider = $releaseProvider;
        $this->github = app(GitHub::class, [
            self::name()
        ]);
        $this->filesystem = $filesystem;
        $this->git = $git;
    }

    public function publish(Version $version)
    {
        $sha1 = $this->sha1($version);

        $gitConfig = config('games.'.self::name().'.github');
        $repository = new Repository($gitConfig['namespace'], $gitConfig['repository']);
        $repository = $this->git->clone($repository);

        # Update the version
        $dockerfilePath = $repository->path().'/Dockerfile';
        $dockerfile = $this->filesystem->get($dockerfilePath);

        $replacements = [
            "/VERSION=(.*?) \\\\/"        => 'VERSION='.$version->version().' \\',
            "/FACTORIO_SHA1=(.*?) \\\\/"  => 'FACTORIO_SHA1='.$sha1.' \\',
        ];
        $dockerfile = preg_replace(array_keys($replacements), array_values($replacements), $dockerfile);
        
        $this->filesystem->put($dockerfilePath, $dockerfile);

        $repository->commit('Updated to '.$version->patchTag());
        $repository->push();

        $this->filesystem->delete($repository->path());

        parent::publish($version);
    }

    public function unpublishedVersions() : Collection
    {
        /** @var Releases $releases */
        $releases = $this->releaseProvider->releases();

        return $releases->all()->filter(function (Version $release) {
            return $this->github->hasNotBeenReleased($release->patchTag()) && $this->github->hasNotBeenReleased($release->patchTag().'-experimental');
        });
    }

    /**
     * Get a sha1 for a version's build
     */
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
