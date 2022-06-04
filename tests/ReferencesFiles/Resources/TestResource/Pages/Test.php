<?php

namespace DummyVendor\DummyPackageValid\Filament\Resources\TestResource\Pages;

use DummyVendor\DummyPackageValid\Filament\Resources\TestResource;
use Filament\Resources\Pages\Page;

class Test extends Page
{
    protected static string $resource = TestResource::class;

    protected static string $view = 'filament.resources.test-resource.pages.test';
}
