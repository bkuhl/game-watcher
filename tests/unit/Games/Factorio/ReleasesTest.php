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
    public function canDetermineStableVersions()
    {
        $this->assertCount(28, $this->releases->stable());
        $this->assertCount(7, $this->releases->beta());
    }
}