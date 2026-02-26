<?php

declare(strict_types=1);

namespace Verdient\Hyperf3\Database\Listen;

/**
 * 数据库监听器接口
 *
 * @author Verdient。
 */
interface DatabaseListenerInterface
{
    /**
     * 处理函数
     *
     * @param ChangedModels $changedModels 变化的模型集合
     *
     * @author Verdient。
     */
    public function handle(ChangedModels $changedModels): void;
}
