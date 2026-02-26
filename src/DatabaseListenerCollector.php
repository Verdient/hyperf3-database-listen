<?php

declare(strict_types=1);

namespace Verdient\Hyperf3\Database\Listen;

use Hyperf\Di\MetadataCollector;

/**
 * 数据库监听器收集器
 *
 * @method static ?DatabaseListener get(string $key, $default = null)
 * @method static array<class-string<DatabaseListenerInterface>, DatabaseListener> list()
 *
 * @author Verdient。
 */
class DatabaseListenerCollector extends MetadataCollector
{
    /**
     * @inheritdoc
     *
     * @author Verdient。
     */
    protected static array $container = [];

    /**
     * 收集类
     *
     * @param class-string<DatabaseListenerInterface> $className 类名
     * @param DatabaseListener $annotation 注解
     *
     * @author Verdient。
     */
    public static function collectClass(string $className, DatabaseListener $annotation): void
    {
        static::$container[$className] = $annotation;
    }
}
