<?php

declare(strict_types=1);

namespace Verdient\Hyperf3\Database\Listen;

use Psr\Container\ContainerInterface;

/**
 * 调度器管理器
 *
 * @author Verdient。
 */
class DispatcherManager
{
    /**
     * @var array<string,class-string<AbstractDatabaseEventDispatcher>> 数据库事件调度器集合
     *
     * @author Verdient。
     */
    protected static array $dispatchers = [];

    /**
     * @param ContainerInterface $container 容器
     *
     * @author Verdient。
     */
    public function __construct(protected ContainerInterface $container) {}

    /**
     * 添加数据库事件调度器
     *
     * @param string $driver 驱动名称
     * @param class-string<AbstractDatabaseEventDispatcher> $dispatcherClass 数据库事件调度器类名
     *
     * @author Verdient。
     */
    public static function register(string $driver, string $dispatcherClass): void
    {
        static::$dispatchers[$driver] = $dispatcherClass;
    }

    /**
     * 获取数据库事件调度器
     *
     * @param string $driver 驱动名称
     *
     * @return ?class-string<AbstractDatabaseEventDispatcher>
     * @author Verdient。
     */
    public static function get(string $driver): ?string
    {
        return static::$dispatchers[$driver] ?? null;
    }

    /**
     * 获取数据库事件调度器
     *
     * @return array<string,class-string<AbstractDatabaseEventDispatcher>>
     */
    public static function all(): array
    {
        return static::$dispatchers;
    }
}
