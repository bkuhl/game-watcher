# Game Watcher

Watches games for new releases of their dedicated game server clients and creates GitHub releases on a relevant repository.  This allows a repository to utilize Travis-CI to build docker containers for the dedicated server client. 

## Supported Games

 * [Factorio](https://www.factorio.com)

## Installation

 * Run the [latest version's docker container](https://hub.docker.com/r/bkuhl/game-watcher/)
 * Set the following environment variables:
 
    * `RELEASER_NAME` & `RELEASER_EMAIL` - The github account that will be credited with releases 
    * `GITHUB_TOKEN` - Token with access to [create releases](https://help.github.com/articles/creating-releases/) on the github repositories
    * Each of the game-specific environment variables listed below to signify the GitHub repository to attach releases for. 
 
**Factorio**
 * `FACTORIO_GITHUB_NAMESPACE`
 * `FACTORIO_GITHUB_REPOSITORY`

## Adding a game

 * Configure a GitHub "connection" for the game in `config/github.php`.
 * Configure a destination repository for new version release notifications and configure it within `config/games.php` with any additional game-specific configuration.
 * Add a namespace for the game under `app/Games` with a game file that's named after the namespace and implements the ReleasesVersions interface.  Here's an example:
 
 ```
 <?php
 
 namespace App\Games\Factorio;

use App\Games\PublishesVersions;

class Factorio extends PublishesVersions
 {
     const NAME = 'factorio';
 
     public function unreleasedVersions() : array
     {
        // code to determine the unreleased versions
     }
     
     public function name() : string
     {
        return self::NAME;
     }
 }
 ```