# Filament Plugin Tools

[![Latest Version on Packagist](https://img.shields.io/packagist/v/ibrostudio/filament-plugin-tools.svg?style=flat-square)](https://packagist.org/packages/ibrostudio/filament-plugin-tools)
[![GitHub Tests Action Status](https://img.shields.io/github/workflow/status/ibrostudio/filament-plugin-tools/run-tests?label=tests)](https://github.com/ibrostudio/filament-plugin-tools/actions?query=workflow%3Arun-tests+branch%3Amain)
[![GitHub Code Style Action Status](https://img.shields.io/github/workflow/status/ibrostudio/filament-plugin-tools/Check%20&%20fix%20styling?label=code%20style)](https://github.com/ibrostudio/filament-plugin-tools/actions?query=workflow%3A"Check+%26+fix+styling"+branch%3Amain)

This package is for [Filament](https://filamentphp.com/) plugin development.

It simply converts the native Filament commands to a plugin context to generate resources, pages and widgets.

## Requirements

Let's say you are developing a plugin called PluginName.
Files are located in a dedicated directory, called packages.
You have locally imported the plugin in a Laravel app for development:

In *composer.json*:
```json
{
    "repositories": [
        {
            "type": "path",
            "url": "../../packages/plugin-name"
        }
    ]
}
```

Then:
```bash
composer require vendor-name/plugin-name
```

##### Auto-registering
This package can auto-register resources, pages and widgets in your plugin service provider.

For that, ensure that:

1. You added your service provider's fully qualified class name to the *extra.laravel.providers* array in your plugin's *composer.json* file:
```json
{
    "extra": {
        "laravel": {
            "providers": [
                "VendorName\\PluginName\\PluginNameServiceProvider"
            ]
        }
    }
}
```

2. Add the properties ```$pages```, ```$resources``` and ```$widgets``` in the plugin's service provider file:
```php
use Filament\PluginServiceProvider;
use Spatie\LaravelPackageTools\Package;

class PluginNameServiceProvider extends PluginServiceProvider
{
    protected array $pages = [
    ];

    protected array $resources = [
    ];

    protected array $widgets = [
    ];

    public function configurePackage(Package $package): void
    {
        $package->name('plugin-name');
    }
}
```

## Installation

Install the filament-plugin-tools package in your Laravel app via composer:

```bash
composer require ibrostudio/filament-plugins-tools --dev
```

## Usage

##### Resources
```bash
php artisan make:filament-plugin-resource plugin-name Model
```

The options work as with the original commands:
```bash
php artisan make:filament-plugin-resource plugin-name Model --simple
php artisan make:filament-plugin-resource plugin-name Model --generate
php artisan make:filament-plugin-resource plugin-name Model --view-page
etc...
```

##### Relation managers
All relations managers are availables:
```bash
php artisan make:filament-plugin-has-many ModelResource relationship attribute
php artisan make:filament-plugin-has-many-through ModelResource relationship attribute
php artisan make:filament-plugin-belongs-to-many ModelResource relationship attribute
php artisan make:filament-plugin-morph-many ModelResource relationship attribute
php artisan make:filament-plugin-morph-to-many ModelResource relationship attribute
```

##### Pages
```bash
php artisan make:filament-plugin-page plugin-name PageName
```

Options work too:
```bash
php artisan make:filament-plugin-page plugin-name Model --resource=ModelResource --type=custom
```

##### Widgets
```bash
php artisan make:filament-plugin-widget plugin-name WidgetName
```

With resource option:
```bash
php artisan make:filament-plugin-widget plugin-name Model --resource=ModelResource
```

## Testing

```bash
composer test
```

## Credits

- [iBroStudio](https://github.com/iBroStudio)
- [The Filament team](https://filamentphp.com/), thanks to them for their work

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
