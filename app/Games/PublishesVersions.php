<?php

namespace App\Games;

use Illuminate\Support\Collection;

interface PublishesVersions
{
    public function name() : string;

    public function unpublishedVersions() : Collection;
}