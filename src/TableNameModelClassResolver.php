<?php

declare(strict_types=1);

namespace Verdient\Hyperf3\Database\Listen;

use Override;
use Verdient\Hyperf3\Database\Model\ModelInterface;

/**
 * 数据表名称模型类解析器
 *
 * @author Verdient。
 */
class TableNameModelClassResolver implements ModelClassResolverInterface
{
    /**
     * 表名和模型的映射关系
     *
     * @author Verdient。
     */
    protected array $classes;

    /**
     * @param class-string<ModelInterface>[] $models 模型集合
     *
     * @author Verdient。
     */
    public function __construct(array $models)
    {
        $classes = [];

        foreach ($models as $model) {
            $classes[$model::tableName()] = $model;
        }

        $this->classes = $classes;
    }

    /**
     * @author Verdient。
     */
    #[Override]
    public function resolve(string $identifier): ?string
    {
        return $this->classes[$identifier] ?? null;
    }
}
