<?php

declare(strict_types=1);

namespace Verdient\Hyperf3\Database\Listen;

use Verdient\Hyperf3\Database\Model\DefinitionManager;
use Verdient\Hyperf3\Database\Model\ModelInterface;
use Verdient\Hyperf3\Database\Model\PrimaryKeys;

/**
 * 事件模型集合
 *
 * @author Verdient。
 */
class EventModels
{
    /**
     * @var array<string,ModelInterface> 事件变化的模型集合
     *
     * @author Verdient。
     */
    protected array $models = [];

    /**
     * 主键定义
     *
     * @author Verdient。
     */
    protected PrimaryKeys $primaryKeys;

    /**
     * @param Event $event 事件
     * @param class-string<ModelInterface> $modelClass 模型类名
     *
     * @author Verdient。
     */
    public function __construct(
        public readonly Event $event,
        public readonly string $modelClass
    ) {
        $this->primaryKeys = DefinitionManager::get($modelClass)->primaryKeys;
    }

    /**
     * 添加模型
     *
     * @param ModelInterface $model 模型
     *
     * @author Verdient。
     */
    public function add(ModelInterface $model): static
    {
        $identifier = $this->getModelIdentifier($model);

        $this->models[$identifier] = $model;

        return $this;
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
        $values = [];

        foreach ($this->primaryKeys->all() as $primaryKey) {
            $values[] = $primaryKey->property->getValue($model);
        }

        return md5(serialize($values));
    }

    /**
     * 获取所有的模型
     *
     * @return ?array<string,ModelInterface>
     * @author Verdient。
     */
    public function all(): ?array
    {
        return $this->models;
    }

    /**
     * 获取模型数量
     *
     * @author Verdient。
     */
    public function count(): int
    {
        return count($this->models);
    }
}
