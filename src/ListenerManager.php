<?php

declare(strict_types=1);

namespace Verdient\Hyperf3\Database\Listen;

use Verdient\Hyperf3\Database\Model\ModelInterface;

/**
 * 监听器管理器
 *
 * @author Verdient。
 */
class ListenerManager
{
    /**
     * 监听器集合
     *
     * @author Verdient。
     */
    protected static ?array $listeners = null;

    /**
     * 初始化
     *
     * @author Verdient。
     */
    protected static function init()
    {
        if (static::$listeners === null) {
            static::$listeners = [];
            foreach (DatabaseListenerCollector::list() as $class => $annotation) {
                foreach ($annotation->modelClasses as $modelClass) {
                    if (isset(static::$listeners[$modelClass])) {
                        static::$listeners[$modelClass][] = $class;
                    } else {
                        static::$listeners[$modelClass] = [$class];
                    }
                }
            }
        }
    }

    /**
     * 是否存在指定类的监听器
     *
     * @param class-string<ModelInterface> 模型类名
     *
     * @author Verdient。
     */
    public static function has(string $modelClass): bool
    {
        static::init();
        return isset(static::$listeners[$modelClass]);
    }

    /**
     * 获取指定类的监听器集合
     *
     * @param class-string<ModelInterface> 模型类名
     *
     * @return array<int,class-string<DatabaseListenerInterface>>|null
     * @author Verdient。
     */
    public static function get(string $modelClass): ?array
    {
        static::init();
        return static::$listeners[$modelClass] ?? null;
    }

    /**
     * 获取所有的监听器
     *
     * @return array<string,array<int,class-string<DatabaseListenerInterface>>>
     * @author Verdient。
     */
    public static function all(): array
    {
        static::init();
        return static::$listeners;
    }
}
