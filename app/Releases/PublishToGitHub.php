<?php

namespace App\Releases;

use App\Games\PublishesVersions;
use Carbon\Carbon;
use GrahamCampbell\GitHub\Facades\GitHub;
use GrahamCampbell\GitHub\GitHubManager;
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

    public function handle(GitHubManager $github)
    {
        $githubConfig = $this->game->gitHubConfig();

        $github = $github->connection($this->game->name());
        $masterBranch = $github->repo()->branches($githubConfig['namespace'], $githubConfig['repository'], 'master');
        $github->git()->tags()->create(
            $githubConfig['namespace'],
            $githubConfig['repository'],
            [
                'tag'       => $this->version->patchTag(),
                'tagger'    => [
                    'name'  => config('github.tagger.name'),
                    'email' => config('github.tagger.email'),
                    'date'  => Carbon::now()->toAtomString()
                ],
                'message'   => 'This release was automatically published by [Game-Watcher](https://github.com/bkuhl/game-watcher).',
                'object'    => $masterBranch['commit']['sha'],
                'type'      => 'commit'
            ]
        );

        $github->repo()->releases()->create(
            $githubConfig['namespace'],
            $githubConfig['repository'],
            [
                'tag_name'  => $this->version->patchTag()
            ]
        );
    }
}