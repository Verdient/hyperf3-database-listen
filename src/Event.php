<?php

declare(strict_types=1);

namespace Verdient\Hyperf3\Database\Listen;

/**
 * 事件
 *
 * @author Verdient。
 */
enum Event
{
    case INSERT;
    case UPDATE;
    case DELETE;
}
