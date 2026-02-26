<?php

declare(strict_types=1);

namespace Verdient\Hyperf3\Database\Listen;

use Attribute;
use Hyperf\Di\Annotation\AbstractAnnotation;
use InvalidArgumentException;
use Override;
use TypeError;
use Verdient\Hyperf3\Database\Model\ModelInterface;

/**
 * 数据库监听器
 *
 * @author Verdient。
 */
#[Attribute(Attribute::TARGET_CLASS)]
class DatabaseListener extends AbstractAnnotation
{
    /**
     * @var array<int,class-string<ModelInterface>> 要监听的模型集合
     *
     * @author Verdient。
     */
    public readonly array $modelClasses;

    /**
     * @param class-string<ModelInterface>[]|string 要监听的模型集合
     *
     * @author Verdient。
     */
    public function __construct(array|string $modelClasses)
    {
        if (is_string($modelClasses)) {
            $modelClasses = [$modelClasses];
        }

        $this->modelClasses = array_values(array_unique($modelClasses));

        if (count($modelClasses) !== count($this->modelClasses)) {
            throw new InvalidArgumentException('Duplicate model class in #[DatabaseListener]');
        }
    }

    /**
     * @author Verdient。
     */
    #[Override]
    public function collectClass(string $className): void
    {
        if (!is_subclass_of($className, DatabaseListenerInterface::class)) {
            throw new TypeError('The class ' . $className . ' with #[DatabaseListener] must implement ' . DatabaseListenerInterface::class . '.');
        }
        DatabaseListenerCollector::collectClass($className, $this);
    }
}
