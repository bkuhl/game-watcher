<?php

namespace App\Games;

use App\Releases\PublishRelease;
use App\Releases\Version;
use Illuminate\Console\Command;

class GameUpdater extends Command
{

    const COMMAND = 'watcher:update-games';

    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = self::COMMAND;

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = "Begin the next season if the current date indicates it's time";

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function fire()
    {
        foreach (array_keys(config('games')) as $gameName) {
            $gameNamespace = title_case($gameName);
            $this->info('Updating '.$gameNamespace.'...');

            // Resolve the game class
            /** @var PublishesVersions $game */
            $game = app('App\Games\\'.$gameNamespace.'\\'.$gameNamespace);
            /** @var Version $version */
            foreach ($game->unpublishedVersions() as $version) {
                try {
                    dispatch(new PublishRelease($game, $version));
                    $this->info('     '.$version->patchTag().' released');
                } catch (\Exception $e) {
                    $this->error('     '.$version->patchTag().' failed');
                    app('log')->error($e);
                }
            }
        }
    }
}
