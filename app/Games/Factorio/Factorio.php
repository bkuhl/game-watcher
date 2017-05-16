<?php

namespace App\Games\Factorio;

use App\VCS\Git;
use App\VCS\GitHub;
use App\Games\PublishesVersions;
use App\Releases\Version;
use App\VCS\Repository;
use Illuminate\Contracts\Logging\Log;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Collection;

class Factorio implements PublishesVersions
{
    const NAME = 'factorio';

    /** @var ReleaseProvider */
    protected $releaseProvider;

    /** @var Repository */
    protected $forkRepo;

    /** @var Repository */
    protected $destinationRepo;

    /** @var GitHub */
    protected $destination;

    /** @var Filesystem */
    protected $filesystem;

    /** @var Git */
    protected $git;

    /** @var Log */
    protected $log;

    public function __construct(ReleaseProvider $releaseProvider, Filesystem $filesystem, Git $git)
    {
        $this->releaseProvider = $releaseProvider;

        $gitConfig = config('games.'.self::name());
        $this->destinationRepo = new Repository($gitConfig['github']['namespace'], $gitConfig['github']['repository']);
        $this->destination = new GitHub(self::name(), $this->destinationRepo);
        $this->filesystem = $filesystem;
        $this->git = $git;
        $this->log = app('log');
    }

    public function publish(Version $version)
    {
        $repository = $this->git->clone($this->destinationRepo);

        // branch name will be something like "release-v0.15.3-experimental"
        $branch = 'update-'.$version->patchTag();
        $this->git->newBranch($repository, $branch);

        // if there's not a Dockerfile, assume this isn't a supported version and/or is a major release
        // that needs to be done manually
        $dockerfilePath = $repository->path().'/'.$version->major().'.'.$version->minor().'/Dockerfile';
        if ($this->filesystem->exists($dockerfilePath)) {
            // sha1 disabled since this doesn't change between major versions
            $sha1 = $this->sha1($version);

            // update Dockerfile with latest details
            $dockerfile = $this->filesystem->get($dockerfilePath);
            $originalContentsHash = md5($dockerfile);
            $replacements = [
                "/VERSION=(.*?) \\\\/"  => 'VERSION='.$version->version().' \\',
                "/SHA1=(.*)/"          => 'SHA1='.$sha1,
            ];
            $dockerfile = preg_replace(array_keys($replacements), array_values($replacements), $dockerfile);
            $this->filesystem->put($dockerfilePath, $dockerfile);
            $updatedContentsHash = md5($dockerfile);

            if ($originalContentsHash != $updatedContentsHash) {
                // update README with latest version number
                $readmePath = $repository->path().'/README.md';
                $readme = $this->filesystem->get($readmePath);
                $readme = preg_replace('/`('.$version->major().'\.'.$version->minor().')\.\d*`/', '`$1.'.$version->patch().'`', $readme);
                $this->filesystem->put($readmePath, $readme);

                $repository->commit('Updated to '.$version->patchTag());
                $repository->push($branch);

                $this->destination->createPullRequest($this->destinationRepo, 'master', $version);
                $this->log->info('['.$this->name().' - '.$version.'] Pull request created on '.$this->destinationRepo->namespace().'/'.$this->destinationRepo->name());
            } else {
                $this->log->info('['.$this->name().' - '.$version.'] No changes needed');
            }
        }

        // @todo if no Dockerfile exists, copy a later version?

        $this->filesystem->delete($repository->path());
    }

    public function unpublishedVersions() : Collection
    {
        /** @var Releases $releases */
        $releases = $this->releaseProvider->releases();

        // Factorio only has the latest binaries available via download on their site,
        // so lets fetch the latest patch version for each major/minor
        $latestPatchVersions = collect();
        $releases->all()
            ->groupBy(function (Version $release) {
                return $release->minorTag();
            })->each(function (Collection $minorVersions) use ($latestPatchVersions) {
                $latestPatchVersion = $minorVersions
                    ->sortByDesc(function (Version $release) {
                        return $release->patch();
                    })->first();

                $latestPatchVersions->push($latestPatchVersion);
            });

        return $latestPatchVersions->filter(function (Version $version) {

            if ($this->destination->hasBeenReleased($version)) {
                $this->log->info('['.$this->name().' - '.$version.'] Version has already been released');
                return false;
            }

            if ($this->destination->hasPendingPullRequest($version)) {
                $this->log->info('['.$this->name().' - '.$version.'] Version already has a pending pull request');
                return false;
            }

            return true;
        });
    }

    /**
     * Get a sha1 for a version's build
     */
    public function sha1(Version $version) : string
    {
        $clientUrl = str_replace('{VERSION}', $version->version(), config('games.'.self::name().'.client-url'));
        $tmp = storage_path('app/'.str_slug(self::name()).'_'.$version->patchTag().'_'.uniqid());

        $this->filesystem->copy($clientUrl, $tmp);

        $sha1 = sha1_file($tmp);

        $this->filesystem->delete($tmp);

        return $sha1;
    }

    public function name() : string
    {
        return self::NAME;
    }
}
