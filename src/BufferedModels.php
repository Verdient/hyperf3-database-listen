<?php

declare(strict_types=1);

namespace Verdient\Hyperf3\Database\Listen;

/**
 * 缓冲的模型集合
 *
 * @author Verdient。
 */
class BufferedModels
{
    /**
     * @var array<string,ChangedModels> 变化的模型集合
     *
     * @author Verdient。
     */
    protected array $changedModels = [];

    /**
     * @var array<string,int> 模型数量
     *
     * @author Verdient。
     */
    protected array $counts = [];

    /**
     * 添加模型集合
     *
     * @param string $key 键
     * @param EventModels $eventModels 事件模型集合
     *
     * @author Verdient。
     */
    public function add(string $key, EventModels $eventModels): static
    {
        if (isset($this->changedModels[$key])) {
            $changedModels = $this->changedModels[$key];
            $changedModels->merge($eventModels);
        } else {
            $changedModels = ChangedModels::createFromEventModels($eventModels);
            $this->changedModels[$key] = $changedModels;
        }

        $this->counts[$key] = $changedModels->count();

        return $this;
    }

    /**
     * 获取模型数量
     *
     * @param string $key 键
     *
     * @author Verdient。
     */
    public function count(string $key): int
    {
        return $this->counts[$key] ?? 0;
    }

    /**
     * 清空模型集合
     *
     * @param string $key 键
     *
     * @author Verdient。
     */
    public function clear(string $key): static
    {
        unset($this->changedModels[$key]);
        unset($this->counts[$key]);

        return $this;
    }

    /**
     * 获取变化的模型集合并清空缓冲区
     *
     * @param string $key 键
     *
     * @author Verdient。
     */
    public function getAndClear(string $key): ?ChangedModels
    {
        $result = $this->changedModels[$key] ?? null;

        $this->clear($key);

        return $result;
    }
}
