<?php

use Illuminate\Support\Str;

use function Pest\Laravel\artisan;

uses()->group('relations');

$relations = [
    'belongs-to-many',
    'has-many',
    'has-many-through',
    'morph-many',
    'morph-to-many',
];

it('can generate files', function ($relation) {
    $command = Str::of($relation)->prepend('make:filament-plugin-');

    artisan("{$command} dummy-package-valid TestResource {$relation} name")->assertSuccessful();

    $manager = Str::of($relation)->camel()->ucfirst()->append('RelationManager');

    $this->assertFileExists(base_path("vendor/dummyvendor/dummy-package-valid/src/Filament/Resources/TestResource/RelationManagers/{$manager}.php"));
})->with($relations);

it('replaces placeholders', function ($relation) {
    $command = Str::of($relation)->prepend('make:filament-plugin-');

    artisan("{$command} dummy-package-valid TestResource {$relation} name")->assertSuccessful();

    $manager = Str::of($relation)->camel()->ucfirst()->append('RelationManager');

    $this->assertEquals(
        file_get_contents(__DIR__ . "/ReferencesFiles/Resources/TestResource/RelationManagers/{$manager}.php"),
        file_get_contents(base_path("vendor/dummyvendor/dummy-package-valid/src/Filament/Resources/TestResource/RelationManagers/{$manager}.php"))
    );
})->with($relations);
