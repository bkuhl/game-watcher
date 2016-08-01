<?php

namespace App\VCS;

class Repository
{
    /** @var string */
    protected $name;

    /** @var string */
    protected $namespace;

    /** @var string */
    protected $path;

    public function __construct(string $namespace, string $name)
    {
        $this->namespace = $namespace;
        $this->name = $name;
    }

    public function namespace() : string
    {
        return $this->namespace;
    }

    public function name(): string
    {
        return $this->name;
    }

    public function sshUrl() : string
    {
        return 'git@github.com:'.$this->namespace().'/'.$this->name().'.git';
    }

    public function setPath(string $path)
    {
        $this->path = $path;
    }

    public function path() : string
    {
        return $this->path;
    }

    public function commit(string $message) : bool
    {
        /** @var Git $git */
        $git = app(Git::class);

        return $git->commit($this, $message);
    }

    public function push() : bool
    {
        /** @var Git $git */
        $git = app(Git::class);

        return $git->push($this);
    }
}
