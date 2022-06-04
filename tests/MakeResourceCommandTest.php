<?php

use function Pest\Laravel\artisan;

uses()->group('resource');

it('can generate files', function () {
    artisan('make:filament-plugin-resource dummy-package-valid Test')->assertSuccessful();

    $this->assertFileExists(base_path('vendor/dummyvendor/dummy-package-valid/src/Filament/Resources/TestResource.php'));
    $this->assertFileExists(base_path('vendor/dummyvendor/dummy-package-valid/src/Filament/Resources/TestResource/Pages/CreateTest.php'));
    $this->assertFileExists(base_path('vendor/dummyvendor/dummy-package-valid/src/Filament/Resources/TestResource/Pages/EditTest.php'));
    $this->assertFileExists(base_path('vendor/dummyvendor/dummy-package-valid/src/Filament/Resources/TestResource/Pages/ListTests.php'));
});

it('replaces placeholders', function () {
    artisan('make:filament-plugin-resource dummy-package-valid Test')->assertSuccessful();

    $this->assertEquals(
        file_get_contents(__DIR__ . '/ReferencesFiles/Resources/TestResource.php'),
        file_get_contents(base_path('vendor/dummyvendor/dummy-package-valid/src/Filament/Resources/TestResource.php'))
    );

    $this->assertEquals(
        file_get_contents(__DIR__ . '/ReferencesFiles/Resources/TestResource/Pages/CreateTest.php'),
        file_get_contents(base_path('vendor/dummyvendor/dummy-package-valid/src/Filament/Resources/TestResource/Pages/CreateTest.php'))
    );

    $this->assertEquals(
        file_get_contents(__DIR__ . '/ReferencesFiles/Resources/TestResource/Pages/EditTest.php'),
        file_get_contents(base_path('vendor/dummyvendor/dummy-package-valid/src/Filament/Resources/TestResource/Pages/EditTest.php'))
    );

    $this->assertEquals(
        file_get_contents(__DIR__ . '/ReferencesFiles/Resources/TestResource/Pages/ListTests.php'),
        file_get_contents(base_path('vendor/dummyvendor/dummy-package-valid/src/Filament/Resources/TestResource/Pages/ListTests.php'))
    );
});

it('can generate view resource', function () {
    artisan('make:filament-plugin-resource dummy-package-valid Test --view-page')->assertSuccessful();

    $this->assertFileExists(base_path('vendor/dummyvendor/dummy-package-valid/src/Filament/Resources/TestResource/Pages/ViewTest.php'));

    $this->assertEquals(
        file_get_contents(__DIR__ . '/ReferencesFiles/Resources/TestResource/Pages/ViewTest.php'),
        file_get_contents(base_path('vendor/dummyvendor/dummy-package-valid/src/Filament/Resources/TestResource/Pages/ViewTest.php'))
    );
});

it('can register resource in service provider', function () {
    artisan('make:filament-plugin-resource dummy-package-valid Test')
        ->expectsOutput('TestResource auto-registred in DummyPackageValidServiceProvider!')
        ->assertSuccessful();

        $this->assertStringContainsString(
            'use DummyVendor\DummyPackageValid\Filament\Resources\TestResource;',
            file_get_contents(base_path('vendor/dummyvendor/dummy-package-valid/src/DummyPackageValidServiceProvider.php'))
        );

        $this->assertStringContainsString(
            'TestResource::class',
            file_get_contents(base_path('vendor/dummyvendor/dummy-package-valid/src/DummyPackageValidServiceProvider.php'))
        );
});
