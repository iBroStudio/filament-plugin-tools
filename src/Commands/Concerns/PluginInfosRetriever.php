<?php

namespace IBroStudio\FilamentPluginTools\Commands\Concerns;

use IBroStudio\FilamentPluginTools\Exceptions\InvalidPlugin;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

trait PluginInfosRetriever
{
    protected function getPluginInfos(string $plugin_name): array
    {
        $installed = json_decode(
            File::get(
                base_path('vendor/composer/installed.json')
            )
        , true);

        $packages = $installed['packages'] ?? $installed;

        $plugin_infos = collect($packages)
            ->filter(function ($package) use ($plugin_name) {

                return Str::endsWith($package['name'], "/$plugin_name");
            })
            ->map(function ($package) {

                if ( ! array_key_exists('autoload', $package) ||  ! array_key_exists('psr-4', $package['autoload'])) {
                    throw InvalidPlugin::invalidComposerFile($package['name']);
                }

                foreach($package['autoload']['psr-4'] as $key => $value) {
                    if ($value === 'src') {
                        $namespace = $key;
                        break;
                    }
                }

                if (empty($namespace)) {
                    throw InvalidPlugin::namespaceNotFound($package['name']);
                }

                $namespace = implode('\\', explode('\\', $namespace, -1));

                $provider = $package['extra']['laravel']['providers'][0] ?? null;
                $provider = Str::replace($namespace . '\\', '', $provider);

                $provider_path = base_path(
                    (string) Str::of($provider)
                        ->prepend('vendor\\' . $package['name'] . '\\src\\')
                        ->replace('\\', '/')
                        ->append('.php'),
                );

                return [
                    'vendor_plugin' => $package['name'],
                    'namespace' => $namespace,
                    'provider' => $provider,
                    'provider_path' => file_exists($provider_path) ? $provider_path : null
                ];
            })
        ->first();

        if (empty($plugin_infos)) {
            throw InvalidPlugin::packageNotFound($plugin_name);
        }

        return $plugin_infos;
    }
}