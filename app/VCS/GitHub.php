<?php

namespace App\VCS;

use App\Releases\Version;
use Github\Client;
use Github\Exception\RuntimeException;
use GrahamCampbell\GitHub\GitHubManager;
use Carbon\Carbon;

class GitHub
{
    /** @var Client */
    protected $github;

    /** @var Repository */
    protected $repository;

    /** @var [] */
    protected $pullRequestsCache;

    public function __construct($game, Repository $repository)
    {
        $this->github = app(GitHubManager::class)->connection($game);
        $this->repository = $repository;
    }

    public function hasBeenReleased(Version $version) : bool
    {
        return $this->hasTag($version->patchTag()) || $this->hasTag('v'.$version->version()) || $this->hasTag($version->version());
    }

    public function hasNotBeenReleased(Version $version) : bool
    {
        return !$this->hasBeenReleased($version);
    }

    /**
     * Creates a new release for the given version
     */
    public function release(Version $version)
    {
        $this->github->git()->tags()->create(
            $this->repository->namespace(),
            $this->repository->name(),
            [
                'tag'       => $version->patchTag(),
                'tagger'    => [
                    'name'  => config('github.tagger.name'),
                    'email' => config('github.tagger.email'),
                    'date'  => Carbon::now()->toAtomString()
                ],
                'message'   => 'This release was automatically published by [Game-Watcher](https://github.com/bkuhl/game-watcher).',
                'object'    => $this->lastCommit('master'),
                'type'      => 'commit'
            ]
        );

        $this->github->repo()->releases()->create(
            $this->repository->namespace(),
            $this->repository->name(),
            [
                'name'      => $version->patchTag(),
                'message'   => 'This release was automatically published by [Game-Watcher](https://github.com/bkuhl/game-watcher).',
                'tag_name'  => $version->patchTag()
            ]
        );
    }

    public function createPullRequest(Repository $from, string $destinationBranch, Version $version)
    {
        $this->github->pullRequests()->create(
            $this->repository->namespace(),
            $this->repository->name(),
            [
                'title'                     => $this->pullRequestTitle($version),
                'body'                      => 'This pull request updates the Dockerfile to support **'.$version->patchTag().'** and was automatically created by [Game-Watcher](https://github.com/bkuhl/game-watcher).',

                // name of the branch where changes are implemented
                'head'                      => $from->namespace().':update-'.$version->patchTag(),

                // name of the branch changes should be pulled into
                'base'                      => $destinationBranch,

                'maintainer_can_modify'     => true
            ]
        );
    }

    public function hasPendingPullRequest(Version $version) : bool
    {
        if ($this->pullRequestsCache == null) {
            $this->pullRequestsCache = $this->github->pullRequests()->all(
                $this->repository->namespace(),
                $this->repository->name()
            );
        }

        // make sure we haven't already submitted a pull request
        $expectedSubmitter = config('github.tagger.name');
        $expectedTitle = $this->pullRequestTitle($version);
        foreach ($this->pullRequestsCache as $pullRequest) {
            if ($expectedTitle == $pullRequest['title'] && $pullRequest['user']['login'] == $expectedSubmitter) {
                return true;
            }
        }

        return false;
    }

    /**
     * Get the last commit sha on a given branch
     */
    public function lastCommit($branch) : string
    {
        return $this->github->repo()->branches($this->repository->namespace(), $this->repository->name(), $branch)['commit']['sha'];
    }

    private function hasTag($tag) : bool
    {
        try {
            $repo = $this->github
            ->repo()
            ->releases()
            ->tag($this->repository->namespace(), $this->repository->name(), $tag);
        } catch (RuntimeException $e) {
            // tag doesn't exist
            if ($e->getCode() == 404) {
                return false;
            }
        }

        return isset($repo) && $repo['tag_name'] == $tag;
    }

    private function pullRequestTitle(Version $version) : string
    {
        return 'Update to '.$version->patchTag();
    }
}
