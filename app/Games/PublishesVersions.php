<?php

namespace App\Games;

use App\Releases\Version;
use Illuminate\Support\Collection;

interface PublishesVersions
{
    public function publish(Version $version);

    public function name() : string;

    public function unpublishedVersions() : Collection;
}
