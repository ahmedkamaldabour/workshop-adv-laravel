<?php

namespace App\Services\Helpers;

use Illuminate\Contracts\Container\BindingResolutionException;
use InvalidArgumentException;

abstract class FactorySelector
{
    // Registry map per concrete selector class: [ selectorClass => [ key => resolver ] ]
    private static array $registries = [];

    /**
     * Return the fully-qualified class name that resolved factories must be instances of.
     * Example: return \App\Services\Maintenance\MaintenanceRequestFactory::class;
     */
    abstract public static function expectedBaseClass(): string;

    /**
     * Optionally override in subclass to register default factories.
     */
    public static function bootDefaults(): void
    {
        // default: no-op. Subclasses should override to register defaults.
    }

    /**
     * Get the registry array for the called selector class (by reference).
     * Ensures each concrete selector has its own registry.
     *
     * @return array<string, string|callable>&
     */
    protected static function &registry(): array
    {
        $class = static::class;

        if (!isset(self::$registries[$class])) {
            self::$registries[$class] = [];
        }

        return self::$registries[$class];
    }

    /**
     * Register a factory resolver for a key. Resolver may be a class-string or a callable returning an instance.
     * Enforces that class-string extends the expected base class when provided as a string.
     */
    public static function registerFactory(string $key, string|callable $resolver): void
    {
        $key = strtolower($key);

        if (is_string($resolver)) {
            if (!class_exists($resolver) || !is_a($resolver, static::expectedBaseClass(), true)) {
                throw new InvalidArgumentException('Factory class must exist and extend ' . static::expectedBaseClass());
            }
        } elseif (!is_callable($resolver)) {
            throw new InvalidArgumentException('Resolver must be a class-string or a callable returning an instance of ' . static::expectedBaseClass());
        }

        $reg = &static::registry();
        $reg[$key] = $resolver;
    }

    /**
     * Check if a key is registered in this selector's registry.
     */
    public static function hasFactory(string $key): bool
    {
        $key = strtolower($key);
        $reg = static::registry();
        return isset($reg[$key]);
    }

    /**
     * Return an instance resolved for the given key.
     * Resolves class-strings via the container (app()->make) and callables by invoking them.
     * Ensures the returned instance matches the expected base class.
     *
     * @throws InvalidArgumentException|BindingResolutionException
     */
    public static function getFactory(string $key): mixed
    {
        $key = strtolower($key);
        $reg = &static::registry();

        if (!isset($reg[$key])) {
            // allow subclass to register defaults lazily
            static::bootDefaults();

            // refresh registry reference in case bootDefaults changed it
            $reg = &static::registry();

            if (!isset($reg[$key])) {
                throw new InvalidArgumentException("Unknown factory key: {$key}");
            }
        }

        $resolver = $reg[$key];

        if (is_string($resolver)) {
            // resolve via the container to allow DI
            $instance = app()->make($resolver);
        } else {
            $instance = $resolver();
        }

        $expected = static::expectedBaseClass();
        if (!is_a($instance, $expected)) {
            throw new InvalidArgumentException('Factory resolver must return an instance of ' . $expected);
        }

        return $instance;
    }

    /**
     * Return a list of registered keys for this selector.
     *
     * @return array<string>
     */
    public static function keys(): array
    {
        $reg = static::registry();
        return array_keys($reg);
    }
}