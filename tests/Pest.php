<?php

use IBroStudio\FilamentPluginTools\Tests\TestCase;
use Illuminate\Support\Facades\File;

uses(TestCase::class)
    ->beforeEach(function () {
        $this->assertDirectoryExists(base_path());
        if (File::isDirectory(base_path('vendor'))) {
            File::deleteDirectory(base_path('vendor'));
        }
        $this->assertDirectoryDoesNotExist(base_path('vendor'));
        File::copyDirectory(__DIR__ . '/DummyFiles/vendor', base_path('vendor'));
        expect(mkdir(base_path('vendor'), 0777, true))->toBe(true);
        $this->assertDirectoryExists(base_path('vendor'));
    })
    ->afterEach(function () {
        File::deleteDirectory(base_path('vendor'));
    })
    ->in(__DIR__);