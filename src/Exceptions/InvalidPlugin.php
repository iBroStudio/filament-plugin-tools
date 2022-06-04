<?php

namespace IBroStudio\FilamentPluginTools\Exceptions;

use Exception;

class InvalidPlugin extends Exception
{
    public static function invalidComposerFile(string $vendor_plugin): self
    {
        return new static("Composer.json for $vendor_plugin doesn't seem to be valid");
    }

    public static function namespaceNotFound(): self
    {
        return new static('Namespace not found. Please check [autoload][psr-4] for "src" value in composer.json');
    }

    public static function packageNotFound(string $plugin_name): self
    {
        return new static("Plugin not found : $plugin_name");
    }
}
