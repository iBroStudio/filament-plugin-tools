<?php

namespace IBroStudio\FilamentPluginTools\Commands;

use IBroStudio\FilamentPluginTools\Commands\Concerns\CanRegister;
use IBroStudio\FilamentPluginTools\Commands\Concerns\PluginInfosRetriever;
use IBroStudio\FilamentPluginTools\Exceptions\InvalidPlugin;
use Illuminate\Console\Command;
use Illuminate\Support\Str;

class MakeWidgetCommand extends Command
{
    use Concerns\CanManipulateFiles;
    use Concerns\CanValidateInput;
    use PluginInfosRetriever;
    use CanRegister;

    protected $description = 'Creates a Filament widget class.';

    protected $signature = 'make:filament-plugin-widget {plugin} {name?} {--R|resource=} {--C|chart} {--T|table} {--S|stats-overview} {--F|force}';

    public function handle(): int
    {
        try {
            $plugin_infos = $this->getPluginInfos($this->argument('plugin'));
        } catch (InvalidPlugin $e) {
            $this->error($e->getMessage());

            return static::FAILURE;
        }

        $widget = (string) Str::of($this->argument('name') ?? $this->askRequired('Name (e.g. `BlogPostsChart`)', 'name'))
            ->trim('/')
            ->trim('\\')
            ->trim(' ')
            ->replace('/', '\\');
        $widgetClass = (string) Str::of($widget)->afterLast('\\');
        $widgetNamespace = Str::of($widget)->contains('\\') ?
            (string) Str::of($widget)->beforeLast('\\') :
            '';

        $resource = null;
        $resourceClass = null;

        $resourceInput = $this->option('resource') ?? $this->ask('(Optional) Resource (e.g. `BlogPostResource`)');

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
        }

        $view = Str::of($widget)
            ->prepend($resource === null ? 'filament\\widgets\\' : "filament\\resources\\{$resource}\\widgets\\")
            ->explode('\\')
            ->map(fn ($segment) => Str::kebab($segment))
            ->implode('.');

        $path = base_path(
            (string) Str::of($widget)
                ->prepend('vendor\\' . $plugin_infos['vendor_plugin'] . '\\src\\Filament\\' . ($resource === null ? 'Widgets\\' : "Resources\\{$resource}\\Widgets\\"))
                ->replace('\\', '/')
                ->append('.php'),
        );

        $viewPath = base_path(
            (string) Str::of($view)
                ->replace('.', '/')
                ->prepend('vendor/' . $plugin_infos['vendor_plugin'] . '/resources/views/')
                ->append('.blade.php'),
        );

        if (! $this->option('force') && $this->checkForCollision([
            $path,
            ($this->option('stats-overview') || $this->option('chart')) ?: $viewPath,
        ])) {
            return static::INVALID;
        }

        if ($this->option('chart')) {
            $chart = $this->choice(
                'Chart type',
                [
                    'Bar chart',
                    'Bubble chart',
                    'Doughnut chart',
                    'Line chart',
                    'Pie chart',
                    'Polar area chart',
                    'Radar chart',
                    'Scatter chart',
                ],
            );

            $this->copyStubToApp('ChartWidget', $path, [
                'class' => $widgetClass,
                'namespace' => $plugin_infos['namespace'] . '\\Filament\\' . (filled($resource) ? "Resources\\{$resource}\\Widgets" . ($widgetNamespace !== '' ? "\\{$widgetNamespace}" : '') : 'Widgets' . ($widgetNamespace !== '' ? "\\{$widgetNamespace}" : '')),
                'plugin_namespace' => $plugin_infos['namespace'],
                'chart' => Str::studly($chart),
            ]);
        } elseif ($this->option('table')) {
            $this->copyStubToApp('TableWidget', $path, [
                'class' => $widgetClass,
                'namespace' => $plugin_infos['namespace'] . '\\Filament\\' . (filled($resource) ? "Resources\\{$resource}\\Widgets" . ($widgetNamespace !== '' ? "\\{$widgetNamespace}" : '') : 'Widgets' . ($widgetNamespace !== '' ? "\\{$widgetNamespace}" : '')),
                'plugin_namespace' => $plugin_infos['namespace'],
            ]);
        } elseif ($this->option('stats-overview')) {
            $this->copyStubToApp('StatsOverviewWidget', $path, [
                'class' => $widgetClass,
                'namespace' => $plugin_infos['namespace'] . '\\Filament\\' . (filled($resource) ? "Resources\\{$resource}\\Widgets" . ($widgetNamespace !== '' ? "\\{$widgetNamespace}" : '') : 'Widgets' . ($widgetNamespace !== '' ? "\\{$widgetNamespace}" : '')),
                'plugin_namespace' => $plugin_infos['namespace'],
            ]);
        } else {
            $this->copyStubToApp('Widget', $path, [
                'class' => $widgetClass,
                'namespace' => $plugin_infos['namespace'] . '\\Filament\\' . (filled($resource) ? "Resources\\{$resource}\\Widgets" . ($widgetNamespace !== '' ? "\\{$widgetNamespace}" : '') : 'Widgets' . ($widgetNamespace !== '' ? "\\{$widgetNamespace}" : '')),
                'plugin_namespace' => $plugin_infos['namespace'],
                'view' => $view,
            ]);

            $this->copyStubToApp('WidgetView', $viewPath);
        }

        $this->info("Successfully created {$widget}!");

        if ($plugin_infos['provider_path']) {
            if ($this->registerWidget($widgetClass, $plugin_infos['provider_path'], $plugin_infos['namespace'])) {
                $this->info("{$widget} auto-registred in {$plugin_infos['provider']}!");
            }
        }

        if ($resource !== null) {
            $this->info("Make sure to register the widget in `{$resourceClass}::getWidgets()`, and then again in `getHeaderWidgets()` or `getFooterWidgets()` of any `{$resourceClass}` page.");
        }

        return static::SUCCESS;
    }
}
