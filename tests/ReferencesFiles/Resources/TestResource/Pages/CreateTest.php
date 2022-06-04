<?php

namespace DummyVendor\DummyPackageValid\Filament\Resources\TestResource\Pages;

use DummyVendor\DummyPackageValid\Filament\Resources\TestResource;
use Filament\Resources\Pages\CreateRecord;

class CreateTest extends CreateRecord
{
    protected static string $resource = TestResource::class;
}
