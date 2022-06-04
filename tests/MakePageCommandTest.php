<?php

use function Pest\Laravel\artisan;

uses()->group('page');

it('can generate files', function () {
    artisan('make:filament-plugin-page dummy-package-valid Test')
        ->expectsQuestion('(Optional) Resource (e.g. `UserResource`)', null)
        ->assertSuccessful();

    $this->assertFileExists(base_path('vendor/dummyvendor/dummy-package-valid/src/Filament/Pages/Test.php'));
    $this->assertFileExists(base_path('vendor/dummyvendor/dummy-package-valid/resources/views/filament/pages/test.blade.php'));
});

it('replaces placeholders', function () {
    artisan('make:filament-plugin-page dummy-package-valid Test')
        ->expectsQuestion('(Optional) Resource (e.g. `UserResource`)', null)
        ->assertSuccessful();

    $this->assertEquals(
        file_get_contents(__DIR__ . '/ReferencesFiles/Pages/Test.php'),
        file_get_contents(base_path('vendor/dummyvendor/dummy-package-valid/src/Filament/Pages/Test.php'))
    );
});

it('can generate custom page for resource', function () {
    artisan('make:filament-plugin-resource dummy-package-valid Test')->assertSuccessful();

    artisan('make:filament-plugin-page dummy-package-valid Test --resource=TestResource --type=custom')->assertSuccessful();

    $this->assertFileExists(base_path('vendor/dummyvendor/dummy-package-valid/src/Filament/Resources/TestResource/Pages/Test.php'));
    $this->assertFileExists(base_path('vendor/dummyvendor/dummy-package-valid/resources/views/filament/resources/test-resource/pages/test.blade.php'));

    $this->assertEquals(
        file_get_contents(__DIR__ . '/ReferencesFiles/Resources/TestResource/Pages/Test.php'),
        file_get_contents(base_path('vendor/dummyvendor/dummy-package-valid/src/Filament/Resources/TestResource/Pages/Test.php'))
    );
});

it('can generate page for resource', function () {
    artisan('make:filament-plugin-resource dummy-package-valid Test')->assertSuccessful();

    artisan('make:filament-plugin-page dummy-package-valid TestPageViewRecord --resource=TestResource --type=ViewRecord')->assertSuccessful();

    $this->assertFileExists(base_path('vendor/dummyvendor/dummy-package-valid/src/Filament/Resources/TestResource/Pages/TestPageViewRecord.php'));

    $this->assertEquals(
        file_get_contents(__DIR__ . '/ReferencesFiles/Resources/TestResource/Pages/TestPageViewRecord.php'),
        file_get_contents(base_path('vendor/dummyvendor/dummy-package-valid/src/Filament/Resources/TestResource/Pages/TestPageViewRecord.php'))
    );
});

it('can register page in service provider', function () {
    artisan('make:filament-plugin-page dummy-package-valid Test')
        ->expectsQuestion('(Optional) Resource (e.g. `UserResource`)', null)
        ->assertSuccessful();

        $this->assertStringContainsString(
            'use DummyVendor\DummyPackageValid\Filament\Pages\Test;',
            file_get_contents(base_path('vendor/dummyvendor/dummy-package-valid/src/DummyPackageValidServiceProvider.php'))
        );

        $this->assertStringContainsString(
            'Test::class',
            file_get_contents(base_path('vendor/dummyvendor/dummy-package-valid/src/DummyPackageValidServiceProvider.php'))
        );
});