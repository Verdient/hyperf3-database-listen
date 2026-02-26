<?php

declare(strict_types=1);

namespace Verdient\Hyperf3\Database\Listen;

use Hyperf\Di\MetadataCollector;

/**
 * 可缓冲收集器
 *
 * @method static ?Buffered get(string $key, $default = null)
 * @method static array<class-string<DatabaseListenerInterface>, Buffered> list()
 *
 * @author Verdient。
 */
class BufferedCollector extends MetadataCollector
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
     * @param Buffered $annotation 注解
     *
     * @author Verdient。
     */
    public static function collectClass(string $className, Buffered $annotation): void
    {
        static::$container[$className] = $annotation;
    }
}
