<?php

declare(strict_types=1);

namespace Verdient\Hyperf3\Database\Listen;

use Verdient\Hyperf3\Database\Model\ModelInterface;

/**
 * 模型类解析器接口
 *
 * @author Verdient。
 */
interface ModelClassResolverInterface
{
    /**
     * 解析模型类
     *
     * @param string $identifier 标识符
     *
     * @return ?class-string<ModelInterface>
     * @author Verdient。
     */
    public function resolve(string $identifier): ?string;
}
