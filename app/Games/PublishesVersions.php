<?php

namespace App\Games;

abstract class PublishesVersions
{
    abstract public function name() : string;

    abstract public function unpublishedVersions() : array;

    public function gitHubConfig() : array
    {
        return config('games.'.$this->name().'.github');
    }
}