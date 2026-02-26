<?php

declare(strict_types=1);

namespace Verdient\Hyperf3\Database\Listen;

use Verdient\Hyperf3\Database\Listen\BeforeMainServerStartListener;
use Verdient\Hyperf3\Database\Listen\BufferedCollector;
use Verdient\Hyperf3\Database\Listen\DatabaseListenerCollector;

class ConfigProvider
{
    public function __invoke(): array
    {
        return [
            'listeners' => [
                BeforeMainServerStartListener::class => 99
            ],
            'annotations' => [
                'scan' => [
                    'collectors' => [
                        BufferedCollector::class,
                        DatabaseListenerCollector::class
                    ]
                ],
            ]
        ];
    }
}
