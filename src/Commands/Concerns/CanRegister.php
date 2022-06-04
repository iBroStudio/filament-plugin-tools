<?php

namespace IBroStudio\FilamentPluginTools\Commands\Concerns;

use Illuminate\Support\Str;

trait CanRegister
{
    protected function registerPage(string $page, string $provider_path, string $namespace): bool
    {
        if (Str::contains(file_get_contents($provider_path), 'protected array $pages =')) {

            return $this->replaceInFile(
                'protected array $pages = [',
                'protected array $pages = [' . "\n\t\t{$page}::class",
                $provider_path
            )

            &&

            $this->replaceInFile(
                "namespace {$namespace};",
                "namespace {$namespace};\n\nuse {$namespace}\Filament\Pages\\{$page};",
                $provider_path
            );
        }
        return false;
    }

    protected function registerResource(string $resource, string $provider_path, string $namespace): bool
    {
        if (Str::contains(file_get_contents($provider_path), 'protected array $resources =')) {

            return $this->replaceInFile(
                'protected array $resources = [',
                'protected array $resources = [' . "\n\t\t{$resource}::class",
                $provider_path
            )

            &&

            $this->replaceInFile(
                "namespace {$namespace};",
                "namespace {$namespace};\n\nuse {$namespace}\Filament\Resources\\{$resource};",
                $provider_path
            );
        }
        return false;
    }

    protected function registerWidget(string $widget, string $provider_path, string $namespace): bool
    {
        if (Str::contains(file_get_contents($provider_path), 'protected array $widgets =')) {

            return $this->replaceInFile(
                'protected array $widgets = [',
                'protected array $widgets = [' . "\n\t\t{$widget}::class",
                $provider_path
            )

            &&

            $this->replaceInFile(
                "namespace {$namespace};",
                "namespace {$namespace};\n\nuse {$namespace}\Filament\Widgets\\{$widget};",
                $provider_path
            );
        }
        return false;
    }

    protected function replaceInFile($search, $replace, $path): bool
    {
        return file_put_contents($path, str_replace($search, $replace, file_get_contents($path)));
    }
}