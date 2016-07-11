<?php

namespace App\Games\Factorio;

use GitHub;
use App\Games\PublishesVersions;
use GrahamCampbell\GitHub\GitHubManager;

class Factorio extends PublishesVersions
{
    const NAME = 'factorio';

    /** @var ReleaseProvider */
    protected $releaseProvider;

    /** @var GitHubManager */
    protected $github;

    public function __construct(
        ReleaseProvider $releaseProvider,
        GitHubManager $github
    ) {
        $this->releaseProvider = $releaseProvider;
        $this->github = $github;
    }

    /**
     * @return array
     */
    public function unpublishedVersions() : array
    {
        /** @var Releases $releases */
        $releases = $this->releaseProvider->releases();

        $github = $this->github->connection($this->name());

        $githubConfig = $this->gitHubConfig();
        $publishedReleases = $github->repo()->releases()->all($githubConfig['namespace'], $githubConfig['repository']);

        $unpublishedReleases = [];
        //dd($releases->all(), $publishedReleases);
        foreach ($releases->all() as $release) {
            foreach ($publishedReleases as $tagName) {
                // tag already exists for this release
                // using the tag so we check "-experimental" builds
                if ($release->patchTag() == $tagName['tag_name']) {
                    continue 2;
                }
            }
            $unpublishedReleases[] = $release;
        }

        return $unpublishedReleases;
    }

    public function name() : string
    {
        return self::NAME;
    }
}