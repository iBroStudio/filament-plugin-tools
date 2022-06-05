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
        //File::copyDirectory(__DIR__ . '/DummyFiles/vendor', base_path('vendor'));
        //expect(mkdir(base_path('vendor'), 0777, true))->toBe(true);
        custom_copy(__DIR__ . '/DummyFiles/vendor', base_path('vendor'));
        $this->assertDirectoryExists(base_path('vendor'));
    })
    ->afterEach(function () {
        File::deleteDirectory(base_path('vendor'));
    })
    ->in(__DIR__);

function custom_copy($src, $dst)
{
    // open the source directory
    $dir = opendir($src);

    // Make the destination directory if not exist
    @mkdir($dst);

    // Loop through the files in source directory
    while( $file = readdir($dir) ) {

        if (( $file != '.' ) && ( $file != '..' )) {
            if ( is_dir($src . '/' . $file) )
            {
                // Recursively calling custom copy function
                // for sub directory
                custom_copy($src . '/' . $file, $dst . '/' . $file);
            }
            else {
                copy($src . '/' . $file, $dst . '/' . $file);
            }
        }
    }

    closedir($dir);
}
