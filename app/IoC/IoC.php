<?php

namespace App\IoC;

class IoC
{
    private static $container = [];
    private static $scopes = [];
    private static $currentScope = 'default';

    public static function Resolve(string $key, ...$args)
    {
        if ($key === 'IoC.Register') {
            return self::register($args[0], $args[1]);
        } elseif ($key === 'Scopes.New') {
            return self::newScope($args[0]);
        } elseif ($key === 'Scopes.Current') {
            return self::setCurrentScope($args[0]);
        } else {
            return self::resolveDependency($key, $args);
        }
    }

    private static function register(string $key, callable $factory)
    {
        self::$container[$key] = $factory;
        return new class {
            public function Execute()
            {
                // Ничего не делаем, просто возвращаем объект для цепочки вызовов
            }
        };
    }

    private static function newScope(string $scopeId)
    {
        self::$scopes[$scopeId] = [];
        return new class {
            public function Execute()
            {
                // Ничего не делаем, просто возвращаем объект для цепочки вызовов
            }
        };
    }

    private static function setCurrentScope(string $scopeId)
    {
        if (!isset(self::$scopes[$scopeId])) {
            throw new \Exception("Scope not found: $scopeId");
        }
        self::$currentScope = $scopeId;
        return new class {
            public function Execute()
            {
                // Ничего не делаем, просто возвращаем объект для цепочки вызовов
            }
        };
    }

    private static function resolveDependency(string $key, array $args)
    {
        if (isset(self::$container[$key])) {
            $factory = self::$container[$key];
            return $factory(...$args);
        }
        throw new \Exception("Dependency not found: $key");
    }

    public static function getCurrentScope()
    {
        return self::$currentScope;
    }

    public static function getScope(string $scopeId)
    {
        return self::$scopes[$scopeId] ?? null;
    }
}
