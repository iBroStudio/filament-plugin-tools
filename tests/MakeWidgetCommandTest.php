<?php

use function Pest\Laravel\artisan;

uses()->group('widget');

it('can generate files', function () {
    artisan('make:filament-plugin-widget dummy-package-valid Test')
        ->expectsQuestion('(Optional) Resource (e.g. `BlogPostResource`)', null)
        ->assertSuccessful();

    $this->assertFileExists(base_path('vendor/dummyvendor/dummy-package-valid/src/Filament/Widgets/Test.php'));
    $this->assertFileExists(base_path('vendor/dummyvendor/dummy-package-valid/resources/views/filament/widgets/test.blade.php'));
});

it('replaces placeholders', function () {
    artisan('make:filament-plugin-widget dummy-package-valid Test')
        ->expectsQuestion('(Optional) Resource (e.g. `BlogPostResource`)', null)
        ->assertSuccessful();

    $this->assertEquals(
        file_get_contents(__DIR__ . '/ReferencesFiles/Widgets/Test.php'),
        file_get_contents(base_path('vendor/dummyvendor/dummy-package-valid/src/Filament/Widgets/Test.php'))
    );
});

it('can generate widget for resource', function () {
    artisan('make:filament-plugin-resource dummy-package-valid Test')->assertSuccessful();

    artisan('make:filament-plugin-widget dummy-package-valid Test --resource=TestResource')->assertSuccessful();

    $this->assertFileExists(base_path('vendor/dummyvendor/dummy-package-valid/src/Filament/Resources/TestResource/Widgets/Test.php'));
    $this->assertFileExists(base_path('vendor/dummyvendor/dummy-package-valid/resources/views/filament/resources/test-resource/widgets/test.blade.php'));

    $this->assertEquals(
        file_get_contents(__DIR__ . '/ReferencesFiles/Resources/TestResource/Widgets/Test.php'),
        file_get_contents(base_path('vendor/dummyvendor/dummy-package-valid/src/Filament/Resources/TestResource/Widgets/Test.php'))
    );
});

it('can register widget in service provider', function () {
    artisan('make:filament-plugin-widget dummy-package-valid Test')
        ->expectsQuestion('(Optional) Resource (e.g. `BlogPostResource`)', null)
        ->assertSuccessful();

    $this->assertStringContainsString(
        'use DummyVendor\DummyPackageValid\Filament\Widgets\Test;',
        file_get_contents(base_path('vendor/dummyvendor/dummy-package-valid/src/DummyPackageValidServiceProvider.php'))
    );

    $this->assertStringContainsString(
        'Test::class',
        file_get_contents(base_path('vendor/dummyvendor/dummy-package-valid/src/DummyPackageValidServiceProvider.php'))
    );
});
