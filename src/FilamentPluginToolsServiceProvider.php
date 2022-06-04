<?php

namespace IBroStudio\FilamentPluginTools;

use IBroStudio\FilamentPluginTools\Commands\MakeBelongsToManyCommand;
use IBroStudio\FilamentPluginTools\Commands\MakeHasManyCommand;
use IBroStudio\FilamentPluginTools\Commands\MakeHasManyThroughCommand;
use IBroStudio\FilamentPluginTools\Commands\MakeMorphManyCommand;
use IBroStudio\FilamentPluginTools\Commands\MakeMorphToManyCommand;
use IBroStudio\FilamentPluginTools\Commands\MakePageCommand;
use IBroStudio\FilamentPluginTools\Commands\MakeResourceCommand;
use IBroStudio\FilamentPluginTools\Commands\MakeWidgetCommand;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class FilamentPluginToolsServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        $package
            ->name('filament-plugin-tools')
            ->hasCommands([
                MakeBelongsToManyCommand::class,
                MakeHasManyCommand::class,
                MakeHasManyThroughCommand::class,
                MakeMorphManyCommand::class,
                MakeMorphToManyCommand::class,
                MakePageCommand::class,
                MakeResourceCommand::class,
                MakeWidgetCommand::class,
            ]);
        ;
    }
}
