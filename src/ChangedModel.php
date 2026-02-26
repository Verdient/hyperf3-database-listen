<?php

declare(strict_types=1);

namespace Verdient\Hyperf3\Database\Listen;

use Verdient\Hyperf3\Database\Model\ModelInterface;

/**
 * 变化的模型
 *
 * @author Verdient。
 */
class ChangedModel
{
    /**
     * @param Event $event 事件
     * @param ModelInterface $model 模型
     *
     * @author Verdient。
     */
    public function __construct(public readonly Event $event, public ModelInterface $model) {}
}
