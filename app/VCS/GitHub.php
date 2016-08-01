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

    public function __construct($game) {
        $this->github = app(GitHubManager::class)->connection($game);
        $this->githubConfig = config('games.'.$game.'.github');
    }

    public function hasBeenReleased(Version $version) : bool
    {
        try {
            $repo = $this->github
                ->repo()
                ->releases()
                ->tag($this->githubConfig['namespace'], $this->githubConfig['repository'], $version->patchTag());
        } catch (RuntimeException $e) {

            // tag doesn't exist
            if ($e->getCode() == 404) {
                return false;
            }

            throw $e;
        }

        return is_array($repo);
    }

    public function hasNotBeenReleased(Version $version) : bool
    {
        return !$this->hasBeenReleased($version);
    }

    public function release(Version $version)
    {
        $this->github->git()->tags()->create(
            $this->githubConfig['namespace'],
            $this->githubConfig['repository'],
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
            $this->githubConfig['namespace'],
            $this->githubConfig['repository'],
            [
                'name'      => $version->patchTag(),
                'message'   => 'This release was automatically published by [Game-Watcher](https://github.com/bkuhl/game-watcher).',
                'tag_name'  => $version->patchTag()
            ]
        );
    }

    /**
     * Get the last commit sha on a given branch
     */
    public function lastCommit($branch) : string
    {
        return $this->github->repo()->branches($this->githubConfig['namespace'], $this->githubConfig['repository'], $branch)['commit']['sha'];
    }
}