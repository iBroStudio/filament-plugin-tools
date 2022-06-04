<?php

namespace IBroStudio\FilamentPluginTools\Commands;

use IBroStudio\FilamentPluginTools\Commands\Concerns\PluginInfosRetriever;
use IBroStudio\FilamentPluginTools\Exceptions\InvalidPlugin;
use Illuminate\Console\Command;
use Illuminate\Support\Str;

class MakeHasManyThroughCommand extends Command
{
    use Concerns\CanManipulateFiles;
    use Concerns\CanValidateInput;
    use PluginInfosRetriever;

    protected $description = 'Creates a Filament HasManyThrough relation manager class for a resource.';

    protected $signature = 'make:filament-plugin-has-many-through {plugin} {resource?} {relationship?} {recordTitleAttribute?} {--F|force}';

    public function handle(): int
    {
        try {
            $plugin_infos = $this->getPluginInfos($this->argument('plugin'));
        } catch (InvalidPlugin $e) {
            $this->error($e->getMessage());

            return static::FAILURE;
        }

        $resource = (string) Str::of($this->argument('resource') ?? $this->askRequired('Resource (e.g. `ProjectResource`)', 'resource'))
            ->studly()
            ->trim('/')
            ->trim('\\')
            ->trim(' ')
            ->replace('/', '\\');

        if (! Str::of($resource)->endsWith('Resource')) {
            $resource .= 'Resource';
        }

        $relationship = (string) Str::of($this->argument('relationship') ?? $this->askRequired('Relationship (e.g. `deployments`)', 'relationship'))
            ->trim(' ');
        $managerClass = (string) Str::of($relationship)
            ->studly()
            ->append('RelationManager');

        $recordTitleAttribute = (string) Str::of($this->argument('recordTitleAttribute') ?? $this->askRequired('Title attribute (e.g. `title`)', 'title attribute'))
            ->trim(' ');

        $path = base_path(
            (string) Str::of($managerClass)
                ->prepend('vendor\\' . $plugin_infos['vendor_plugin'] . "\\src\\Filament\\Resources\\{$resource}\\RelationManagers\\")
                ->replace('\\', '/')
                ->append('.php'),
        );

        if (! $this->option('force') && $this->checkForCollision([
            $path,
        ])) {
            return static::INVALID;
        }

        $this->copyStubToApp('HasManyThroughRelationManager', $path, [
            'namespace' => $plugin_infos['namespace'] . "\\Filament\\Resources\\{$resource}\\RelationManagers",
            'plugin_namespace' => $plugin_infos['namespace'],
            'managerClass' => $managerClass,
            'recordTitleAttribute' => $recordTitleAttribute,
            'relationship' => $relationship,
        ]);

        $this->info("Successfully created {$managerClass}!");

        $this->info("Make sure to register the relation in `{$resource}::getRelations()`.");

        return static::SUCCESS;
    }
}
