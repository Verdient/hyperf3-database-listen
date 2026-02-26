<?php

declare(strict_types=1);

namespace Verdient\Hyperf3\Database\Listen;

use Ds\Map;
use Verdient\Hyperf3\Database\Model\ModelInterface;

/**
 * 变化的模型集合
 *
 * @author Verdient。
 */
class ChangedModels
{

    /**
     * @var array<int,ChangedModel> $changedModels 事件模型集合
     *
     * @author Verdient。
     */
    protected array $changedModels = [];

    /**
     * 创建的模型集合
     *
     * @author Verdient。
     */
    protected array $createdModels = [];

    /**
     * 更新的模型集合
     *
     * @author Verdient。
     */
    protected array $updatedModels = [];

    /**
     * 删除的模型集合
     *
     * @author Verdient。
     */
    protected array $deletedModels = [];

    /**
     * 通过事件模型集合创建
     *
     * @param EventModels $eventModels 事件模型集合
     *
     * @author Verdient。
     */
    public static function createFromEventModels(EventModels $eventModels): static
    {
        $models = $eventModels->all();

        $instance = new static();

        switch ($eventModels->event) {
            case Event::INSERT:
                $instance->createdModels = $models;
                break;
            case Event::UPDATE:
                $instance->updatedModels = $models;
                break;
            case Event::DELETE:
                $instance->deletedModels = $models;
                break;
        }

        $instance->changedModels = array_values(array_map(fn($model) => new ChangedModel($eventModels->event, $model), $models));

        return $instance;
    }

    /**
     * 合并
     *
     * @param EventModels $eventModels 事件模型集合
     *
     * @author Verdient。
     */
    public function merge(EventModels $eventModels): static
    {
        $models = $eventModels->all();

        $removedModels = new Map();

        switch ($eventModels->event) {
            case Event::INSERT:
                foreach ($models as $key => $model) {
                    if (isset($this->createdModels[$key])) {
                        $removedModels->offsetSet($this->createdModels[$key], true);
                    }
                    $this->createdModels[$key] = $model;
                    $this->changedModels[] = new ChangedModel(Event::INSERT, $model);
                }
                break;
            case Event::UPDATE:
                foreach ($models as $key => $model) {
                    if (isset($this->updatedModels[$key])) {
                        $removedModels->offsetSet($this->updatedModels[$key], true);
                    }
                    $this->updatedModels[$key] = $model;
                    $this->changedModels[] = new ChangedModel(Event::UPDATE, $model);
                }
                break;
            case Event::DELETE:
                foreach ($models as $key => $model) {
                    if (isset($this->deletedModels[$key])) {
                        $removedModels->offsetSet($this->deletedModels[$key], true);
                    }
                    $this->deletedModels[$key] = $model;
                    $this->changedModels[] = new ChangedModel(Event::DELETE, $model);
                }
                break;
        }

        foreach ($this->changedModels as $index => $eventModel) {
            if ($removedModels->offsetExists($eventModel->model)) {
                unset($this->changedModels[$index]);
            }
        }

        $this->changedModels = array_values($this->changedModels);

        return $this;
    }

    /**
     * 缓存变化的模型数量
     *
     * @author Verdient。
     */
    public function count()
    {
        return count($this->changedModels);
    }

    /**
     * 获取所有变化的模型
     *
     * @return array<int,ChangedModel>
     * @author Verdient。
     */
    public function all(): array
    {
        return $this->changedModels;
    }

    /**
     * 获取创建的模型
     *
     * @return array<int,ModelInterface>
     * @author Verdient。
     */
    public function created(): array
    {
        return array_values($this->createdModels);
    }

    /**
     * 获取更新的模型
     *
     * @return array<int,ModelInterface>
     * @author Verdient。
     */
    public function updated(): array
    {
        return array_values($this->updatedModels);
    }

    /**
     * 获取删除的模型
     *
     * @return array<int,ModelInterface>
     * @author Verdient。
     */
    public function deleted(): array
    {
        return array_values($this->deletedModels);
    }

    /**
     * 分块
     *
     * @param int $size 分块大小
     *
     * @return ChangedModels[]
     * @author Verdient。
     */
    public function chunk(int $size = 1000): array
    {
        if ($size < 1) {
            $size = 1;
        }

        $result = [];

        $chunk = new static;

        $count = 0;

        foreach ($this->changedModels as $changedModel) {
            $count++;

            switch ($changedModel->event) {
                case Event::INSERT:
                    $chunk->createdModels[] = $changedModel->model;
                    break;
                case Event::UPDATE:
                    $chunk->updatedModels[] = $changedModel->model;
                    break;
                case Event::DELETE:
                    $chunk->deletedModels[] = $changedModel->model;
                    break;
            }

            $chunk->changedModels[] = $changedModel;

            if ($count === $size) {
                $result[] = $chunk;
                $chunk = new static;
                $count = 0;
            }
        }

        if ($count > 0) {
            $result[] = $chunk;
        }

        return $result;
    }
}
