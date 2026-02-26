<?php

declare(strict_types=1);

namespace Verdient\Hyperf3\Database\Listen;

use Attribute;
use Hyperf\Di\Annotation\AbstractAnnotation;
use InvalidArgumentException;
use Override;
use TypeError;

/**
 * 可缓冲的
 *
 * @author Verdient。
 */
#[Attribute(Attribute::TARGET_CLASS)]
class Buffered extends AbstractAnnotation
{
    /**
     * @param int $milliseconds 最大缓存时间
     * @param int $quantity 最大缓存数量
     *
     * @author Verdient。
     */
    public function __construct(
        public readonly int $milliseconds,
        public readonly int $quantity = 1000,
    ) {
        if ($quantity === 0 && $milliseconds === 0) {
            throw new InvalidArgumentException('The quantity and milliseconds cannot be both zero.');
        }

        if ($quantity < 0) {
            throw new InvalidArgumentException('The quantity cannot be less than zero.');
        }

        if ($milliseconds < 0) {
            throw new InvalidArgumentException('The milliseconds cannot be less than zero.');
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
        BufferedCollector::collectClass($className, $this);
    }
}
