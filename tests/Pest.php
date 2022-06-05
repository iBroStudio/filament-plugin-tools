<?php

use IBroStudio\FilamentPluginTools\Tests\TestCase;
use Illuminate\Support\Facades\File;

uses(TestCase::class)
    ->beforeEach(function () {
        if (File::isDirectory(base_path('vendor'))) {
            File::deleteDirectory(base_path('vendor'));
        }
        $this->assertDirectoryDoesNotExist(base_path('vendor'));
        File::copyDirectory(__DIR__ . '/DummyFiles/vendor', base_path('vendor'));
        $this->assertDirectoryExists(base_path('vendor'));
    })
    ->afterEach(function () {
        //File::deleteDirectory(base_path('vendor'));
    })
    ->in(__DIR__);
