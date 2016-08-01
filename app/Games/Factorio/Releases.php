<?php

namespace App\Games\Factorio;

use App\Releases\Version;
use Illuminate\Support\Collection;

class Releases
{

    /** @var Version[] $stable */
    private $stable = [];

    /** @var Version[] $beta */
    private $beta = [];

    /**
     * Look at all releases and classify them as beta or stable
     */
    public function classifyReleases(array $releases) : Releases
    {
        /** @var Version $latestStable */
        $latestStable = app(Version::class, [array_pop($releases)['stable']]);
        foreach ($releases as $release)
        {
            /** @var Version $version */
            $version = app(Version::class, [$release['to']]);
            if ($version->moreRecentThan($latestStable)) {
                $version->setTag('-experimental');
                $this->beta[] = $version;
            } else {
                $this->stable[] = $version;
            }
        }
        
        return $this;
    }

    /**
     * @return Version[]
     */
    public function stable() : Collection
    {
        return collect($this->stable);
    }

    /**
     * @return Version[]
     */
    public function beta() : Collection
    {
        return collect($this->beta);
    }

    /**
     * @return Version[]
     */
    public function all() : Collection
    {
        return $this->beta()->merge($this->stable());
    }
}