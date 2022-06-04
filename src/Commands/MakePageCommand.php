<?php

namespace IBroStudio\FilamentPluginTools\Commands;

use IBroStudio\FilamentPluginTools\Commands\Concerns\CanRegister;
use IBroStudio\FilamentPluginTools\Commands\Concerns\PluginInfosRetriever;
use IBroStudio\FilamentPluginTools\Exceptions\InvalidPlugin;
use Illuminate\Console\Command;
use Illuminate\Support\Str;

class MakePageCommand extends Command
{
    use Concerns\CanManipulateFiles;
    use Concerns\CanValidateInput;
    use PluginInfosRetriever;
    use CanRegister;

    protected $description = 'Creates a Filament page class and view.';

    protected $signature = 'make:filament-plugin-page {plugin} {name?} {--R|resource=} {--T|type=} {--F|force}';

    public function handle(): int
    {
        try {
            $plugin_infos = $this->getPluginInfos($this->argument('plugin'));
        } catch (InvalidPlugin $e) {
            $this->error($e->getMessage());
            return static::FAILURE;
        }

        $page = (string) Str::of($this->argument('name') ?? $this->askRequired('Name (e.g. `Settings`)', 'name'))
            ->trim('/')
            ->trim('\\')
            ->trim(' ')
            ->replace('/', '\\');
        $pageClass = (string) Str::of($page)->afterLast('\\');
        $pageNamespace = Str::of($page)->contains('\\') ?
            (string) Str::of($page)->beforeLast('\\') :
            '';

        $resource = null;
        $resourceClass = null;
        $resourcePage = null;

        $resourceInput = $this->option('resource') ?? $this->ask('(Optional) Resource (e.g. `UserResource`)');

        if ($resourceInput !== null) {
            $resource = (string) Str::of($resourceInput)
                ->studly()
                ->trim('/')
                ->trim('\\')
                ->trim(' ')
                ->replace('/', '\\');

            if (! Str::of($resource)->endsWith('Resource')) {
                $resource .= 'Resource';
            }

            $resourceClass = (string) Str::of($resource)
                ->afterLast('\\');

            $resourcePage = $this->option('type') ?? $this->choice(
                'Which type of page would you like to create?',
                [
                    'custom' => 'Custom',
                    'ListRecords' => 'List',
                    'CreateRecord' => 'Create',
                    'EditRecord' => 'Edit',
                    'ViewRecord' => 'View',
                    'ManageRecords' => 'Manage',
                ],
                'custom',
            );
        }

        $view = Str::of($page)
            ->prepend($resource === null ? 'filament\\pages\\' : "filament\\resources\\{$resource}\\pages\\")
            ->explode('\\')
            ->map(fn ($segment) => Str::kebab($segment))
            ->implode('.');

        $path = base_path(
            (string) Str::of($page)
                ->prepend('vendor\\' . $plugin_infos['vendor_plugin'] . '\\src\\Filament\\' . ($resource === null ? 'Pages\\' : "Resources\\{$resource}\\Pages\\"))
                ->replace('\\', '/')
                ->append('.php'),
        );

        $viewPath = base_path(
            (string) Str::of($view)
                ->replace('.', '/')
                ->prepend('vendor/' . $plugin_infos['vendor_plugin'] . '/resources/views/')
                ->append('.blade.php'),
        );

        $files = array_merge(
            [$path],
            $resourcePage === 'custom' ? [$viewPath] : [],
        );

        if (! $this->option('force') && $this->checkForCollision($files)) {
            return static::INVALID;
        }

        if ($resource === null) {
            $this->copyStubToApp('Page', $path, [
                'class' => $pageClass,
                'namespace' => $plugin_infos['namespace'] . '\\Filament\\Pages' . ($pageNamespace !== '' ? "\\{$pageNamespace}" : ''),
                'plugin_namespace' => $plugin_infos['namespace'],
                'view' => $view,
            ]);
        } else {
            $this->copyStubToApp($resourcePage === 'custom' ? 'CustomResourcePage' : 'ResourcePage', $path, [
                'baseResourcePage' => 'Filament\\Resources\\Pages\\' . ($resourcePage === 'custom' ? 'Page' : $resourcePage),
                'baseResourcePageClass' => $resourcePage === 'custom' ? 'Page' : $resourcePage,
                'namespace' => $plugin_infos['namespace'] . "\\Filament\\Resources\\{$resource}\\Pages" . ($pageNamespace !== '' ? "\\{$pageNamespace}" : ''),
                'plugin_namespace' => $plugin_infos['namespace'],
                'resource' => $resource,
                'resourceClass' => $resourceClass,
                'resourcePageClass' => $pageClass,
                'view' => $view,
            ]);
        }

        if ($resource === null || $resourcePage === 'custom') {
            $this->copyStubToApp('PageView', $viewPath);
        }

        $this->info("Successfully created {$page}!");

        if ($plugin_infos['provider_path']) {
            if ($this->registerPage($pageClass, $plugin_infos['provider_path'], $plugin_infos['namespace'])) {
                $this->info("{$pageClass} auto-registred in {$plugin_infos['provider']}!");
            }
        }

        if ($resource !== null) {
            $this->info("Make sure to register the page in `{$resourceClass}::getPages()`.");
        }

        return static::SUCCESS;
    }
}
