<?php

namespace App\Games;

use App\VCS\GitHub;
use App\Releases\Version;
use Illuminate\Support\Collection;

abstract class PublishesVersions
{

    public function publish(Version $version)
    {

        /** @var GitHub $github */
        $github = app(GitHub::class, [
            $this->name()
        ]);
        $github->release($version);
    }

    abstract public function name() : string;

    abstract public function unpublishedVersions() : Collection;
}
