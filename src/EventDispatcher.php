<?php

declare(strict_types=1);

namespace Verdient\Hyperf3\Database\Listen;

use Swoole\Coroutine;
use Swoole\Timer;
use Verdient\Hyperf3\Database\Model\DefinitionManager;
use Verdient\Hyperf3\Database\Model\ModelInterface;

use function Hyperf\Support\make;

/**
 * 事件调度器
 *
 * @author Verdient。
 */
class EventDispatcher
{
    /**
     * 缓存的模型集合
     *
     * @author Verdient。
     */
    protected BufferedModels $bufferedModels;

    /**
     * 缓存的定时器编号集合
     *
     * @author Verdient。
     */
    protected array $bufferedTimerIds = [];

    /**
     * @var array<class-string<DatabaseListenerInterface>,DatabaseListenerInterface> 缓存的监听器
     *
     * @author Verdient。
     */
    protected array $listeners = [];

    /**
     * @author Verdient。
     */
    public function __construct()
    {
        $this->bufferedModels = new BufferedModels;
    }

    /**
     * 调度
     *
     * @param EventModels $eventModels 事件变化的模型集合
     *
     * @author Verdient。
     */
    public function dispatch(EventModels $eventModels): void
    {
        $listeners = ListenerManager::get($eventModels->modelClass);

        if (empty($listeners)) {
            return;
        }

        $exception = null;

        try {
            foreach ($listeners as $listenerClass) {
                if ($buffered = BufferedCollector::get($listenerClass)) {
                    $this->dispatchBuffered($buffered, $listenerClass, $eventModels);
                } else {
                    $this->dispatchImmediate($listenerClass, $eventModels);
                }
            }
        } catch (\Throwable $e) {
            if ($exception === null) {
                $exception = $e;
            }
        }

        if ($exception !== null) {
            throw $exception;
        }
    }

    /**
     * 即时调度
     *
     * @param class-string<DatabaseListenerInterface> $listenerClass 监听器类
     * @param EventModels $eventModels 事件变化的模型集合
     *
     * @author Verdient。
     */
    protected function dispatchImmediate(string $listenerClass, EventModels $eventModels): void
    {
        $listener = $this->resolveListener($listenerClass);
        Coroutine::create(fn() => $listener->handle(ChangedModels::createFromEventModels($eventModels)));
    }

    /**
     * 缓冲调度
     *
     * @param Buffered $buffered 缓冲
     * @param class-string<DatabaseListenerInterface> $listenerClass 监听器类
     * @param EventModels $eventModels 事件变化的模型集合
     *
     * @author Verdient。
     */
    protected function dispatchBuffered(Buffered $buffered, string $listenerClass, EventModels $eventModels): void
    {
        if (isset($this->bufferedTimerIds[$listenerClass])) {
            Timer::clear($this->bufferedTimerIds[$listenerClass]);
            unset($this->bufferedTimerIds[$listenerClass]);
        }

        $this->bufferedModels->add($listenerClass, $eventModels);

        if ($this->bufferedModels->count($listenerClass) >= $buffered->quantity) {
            unset($this->bufferedTimerIds[$listenerClass]);
            $changedModels = $this->bufferedModels->getAndClear($listenerClass);
            $listener = $this->resolveListener($listenerClass);
            Coroutine::create(fn() => $listener->handle($changedModels));
            return;
        }

        if ($buffered->milliseconds > 0) {
            $this->bufferedTimerIds[$listenerClass] = Timer::after(
                $buffered->milliseconds,
                function () use ($listenerClass) {
                    unset($this->bufferedTimerIds[$listenerClass]);
                    $this->resolveListener($listenerClass)->handle($this->bufferedModels->getAndClear($listenerClass));
                }
            );
        }
    }

    /**
     * 获取模型标识符
     *
     * @param ModelInterface $model 模型
     *
     * @author Verdient。
     */
    protected function getModelIdentifier(ModelInterface $model): string
    {
        $primaryKeyValues = [];

        foreach (
            DefinitionManager::get($model::class)
                ->primaryKeys
                ->all() as $primaryKey
        ) {
            $primaryKeyValues[] = $primaryKey->property->getValue($model);
        }

        return md5(serialize($primaryKeyValues));
    }

    /**
     * 处理监听器
     *
     * @param class-string<DatabaseListenerInterface> $class 类名
     *
     * @author Verdient。
     */
    protected function resolveListener(string $class): DatabaseListenerInterface
    {
        if (!isset($this->listeners[$class])) {
            $this->listeners[$class] = make($class);
        }

        return $this->listeners[$class];
    }
}
