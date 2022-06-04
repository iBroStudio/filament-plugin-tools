<?php

use function Pest\Laravel\artisan;

uses()->group('fails');

$commands = [
    'make:filament-plugin-belongs-to-many',
    'make:filament-plugin-has-many',
    'make:filament-plugin-has-many-through',
    'make:filament-plugin-morph-many',
    'make:filament-plugin-morph-to-many',
    'make:filament-plugin-page',
    'make:filament-plugin-resource',
    'make:filament-plugin-widget',
];

it('fails when plugin can not be found', function ($command) {
    artisan("$command dummy-package-invalid-namespace")
        ->expectsOutput('Namespace not found. Please check [autoload][psr-4] for "src" value in composer.json')
        ->assertFailed();
})->with($commands);

it('fails when composer.json has no [autoload][psr-4]', function ($command) {
    artisan("$command dummy-package-invalid-composer")
        ->expectsOutput('Composer.json for dummyvendor/dummy-package-invalid-composer doesn\'t seem to be valid')
        ->assertFailed();
})->with($commands);

it('fails when namespace can not be identified', function ($command) {
    artisan("$command dummy-package-invalid-namespace")
        ->expectsOutput('Namespace not found. Please check [autoload][psr-4] for "src" value in composer.json')
        ->assertFailed();
})->with($commands);
