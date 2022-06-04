<?php

namespace DummyVendor\DummyPackageValid\Filament\Resources\TestResource\RelationManagers;

use Filament\Resources\Form;
use Filament\Resources\RelationManagers\MorphToManyRelationManager;
use Filament\Resources\Table;

class MorphToManyRelationManager extends MorphToManyRelationManager
{
    protected static string $relationship = 'morph-to-many';

    protected static ?string $recordTitleAttribute = 'name';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                //
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                //
            ])
            ->filters([
                //
            ]);
    }
}
