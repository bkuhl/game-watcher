<?php

namespace App\VCS;

use Illuminate\Filesystem\Filesystem;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\Process;

class Git
{
    /** @var Filesystem */
    protected $filesystem;

    public function __construct(Filesystem $filesystem)
    {
        $this->filesystem = $filesystem;
    }

    public function clone(Repository $repository) : Repository
    {
        $path = $this->temporaryPath();

        /** @var Process $process */
        $process = new Process('git clone "'.$repository->sshUrl().'" "."');
        $process->setWorkingDirectory($path);
        $process->run();

        if (!$process->isSuccessful()) {
            throw new ProcessFailedException($process);
        }

        $repository->setPath($path);

        return $repository;
    }

    public function commit(Repository $repository, $message) : bool
    {
        /** @var Process $process */
        $process = new Process('git commit -a -m "'.$message.'"');
        $process->setWorkingDirectory($repository->path());
        $process->run();

        if (!$process->isSuccessful()) {
            throw new ProcessFailedException($process);
        }

        return true;
    }

    public function push(Repository $repository, string $branch) : bool
    {
        /** @var Process $process */
        $process =  new Process('git push --set-upstream origin '.$branch);
        $process->setWorkingDirectory($repository->path());
        $process->run();

        if (!$process->isSuccessful()) {
            throw new ProcessFailedException($process);
        }

        return true;
    }

    /**
     * New branch will be created off of master
     */
    public function newBranch(Repository $repository, string $name) : bool
    {
        /** @var Process $process */
        $process =  new Process('git checkout -b '.$name.' master');
        $process->setWorkingDirectory($repository->path());
        $process->run();

        if (!$process->isSuccessful()) {
            throw new ProcessFailedException($process);
        }

        return true;
    }

    protected function temporaryPath(): string
    {
        $path = storage_path('git-'.uniqid());

        $this->filesystem->makeDirectory($path);

        return $path;
    }
}
