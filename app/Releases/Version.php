<?php

namespace App\Releases;

class Version
{
    private $tag;

    private $version;

    public function __construct($version, $tag = null)
    {
        // strip non-numeric characters
        $this->version = explode('.', preg_replace('/[^0-9.]/', '', $version));
        $this->tag = $tag;
    }

    public function major(): int
    {
        if (isset($this->version[0])) {
            return intval($this->version[0]);
        }

        return 0;
    }

    public function minor(): int
    {
        if (isset($this->version[1])) {
            return intval($this->version[1]);
        }

        return 0;
    }

    public function patch(): int
    {
        if (isset($this->version[2])) {
            return intval($this->version[2]);
        }

        return 0;
    }

    /**
     * Allows you specify additional tags after the version (i.e. - 1.4.3b)
     */
    public function setTag(string $tag)
    {
        $this->tag = $tag;
    }

    public function tag(): string
    {
        return $this->tag ?: '';
    }

    /**
     * Provides the version down to the minor
     */
    public function minorTag(): string
    {
        return 'v'.implode('.', [
            $this->major(),
            $this->minor()
        ]).$this->tag();
    }

    /**
     * Provides the version down to the patch
     */
    public function patchTag(): string
    {
        return 'v'.$this->version().$this->tag();
    }

    public function version()
    {
        return implode('.', [
            $this->major(),
            $this->minor(),
            $this->patch()
        ]);
    }

    /**
     * Determine if the provided version is more recent than
     * the current version.  Compare Major, Minor, then Patch
     */
    public function moreRecentThan(Version $version): bool
    {
        if ($this->major() > $version->major()) {
            return true;
        } elseif ($this->major() == $version->major()) {

            if ($this->minor() > $version->minor()) {
                return true;
            } elseif ($this->minor() == $version->minor()) {

                if ($this->patch() > $version->patch()) {
                    return true;
                }
            }
        }

        return false;
    }
}
