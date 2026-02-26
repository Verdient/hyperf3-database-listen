<?php

declare(strict_types=1);

namespace Verdient\Hyperf3\Database\Listen;

use Hyperf\Process\AbstractProcess;
use Psr\Container\ContainerInterface;
use Verdient\Hyperf3\Database\Listen\ModelClassResolverInterface;
use Verdient\Hyperf3\Logger\HasLogger;

/**
 * 抽象数据库事件调度器
 *
 * @author Verdient。
 */
abstract class AbstractDatabaseEventDispatcher extends AbstractProcess
{
    use HasLogger;

    /**
     * 重启间隔
     *
     * @author Verdient。
     */
    protected int $restartInterval = 1;

    /**
     * @param ContainerInterface $container 容器
     * @param string $connectionName 连接名称
     * @param string[] $tables 表集合
     *
     * @author Verdient。
     */
    public function __construct(
        ContainerInterface $container,
        protected string $connectionName,
        protected array $tables,
        protected ModelClassResolverInterface $modelClassResolver
    ) {
        parent::__construct($container);

        $this->name = 'Database-Event-Dispatcher-' . $connectionName;
    }
}
