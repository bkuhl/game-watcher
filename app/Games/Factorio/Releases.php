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
                $this->beta[$version->version()] = $version;
            } else {
                $this->stable[$version->version()] = $version;
            }
        }
        
        return $this;
    }

    /**
     * @return Version[]
     */
    public function all() : Collection
    {
        $all = array_merge($this->stable, $this->beta);

        // sort all versions by most recent
        uksort($all, function ($a, $b) {
            $a = new Version($a);
            $b = new Version($b);
            return $a->moreRecentThan($b) ? 1 : -1;
        });

        return collect($all);
    }
}