<?php

namespace App\Games\Factorio;

class ReleasesTest extends \TestCase
{
    /** @var Releases */
    private $releases;

    public function setUp()
    {
        parent::setUp();

        /** @var ReleaseProvider $releasesProvider */
        $releasesProvider = app(ReleaseProvider::class);
        $this->releases = $releasesProvider->releases();
    }

    /**
     * @test
     */
    public function canSortVersionsByMostRecent()
    {
        $all = $this->releases->all();
        $this->assertEquals('v0.12.8', $all->first()->patchTag());
        $this->assertEquals('v0.13.6-experimental', $all->last()->patchTag());
    }
}