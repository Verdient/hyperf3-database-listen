<?php

declare(strict_types=1);

namespace Verdient\Hyperf3\Database\Listen;

use Hyperf\Contract\ConfigInterface;
use Hyperf\Event\Contract\ListenerInterface;
use Hyperf\Framework\Event\BeforeMainServerStart;
use Hyperf\Process\ProcessManager;
use Hyperf\Server\Event\MainCoroutineServerStart;
use InvalidArgumentException;
use Override;
use Psr\Container\ContainerInterface;

use function Hyperf\Support\make;

/**
 * 主进程启动前监听器
 *
 * @author Verdient。
 */
class BeforeMainServerStartListener implements ListenerInterface
{
    /**
     * @author Verdient。
     */
    public function __construct(protected ContainerInterface $container) {}

    /**
     * @author Verdient。
     */
    #[Override]
    public function listen(): array
    {
        return [
            BeforeMainServerStart::class,
            MainCoroutineServerStart::class,
        ];
    }

    /**
     * @param BeforeMainServerStart|MainCoroutineServerStart $event
     *
     * @author Verdient。
     */
    #[Override]
    public function process(object $event): void
    {
        /** @var ConfigInterface */
        $config = $this
            ->container
            ->get(ConfigInterface::class);

        if (!$config->get('dev.database.event_listener.enable', false)) {
            return;
        }

        $connections = [];

        foreach (array_keys(ListenerManager::all()) as $modelClass) {

            $connectionName = $modelClass::connectionName();

            if (isset($connections[$connectionName])) {
                $connections[$connectionName][] = $modelClass;
            } else {
                $connections[$connectionName] = [$modelClass];
            }
        }

        $databaseConfig = $config->get('databases', []);

        foreach ($connections as $connectionName => $modelClasses) {

            if (!isset($databaseConfig[$connectionName])) {
                throw new InvalidArgumentException('Unknown connection name: ' . $connectionName);
            }

            if (!$dispatcher = DispatcherManager::get($databaseConfig[$connectionName]['driver'])) {
                throw new InvalidArgumentException('Unknown database driver: ' . $databaseConfig[$connectionName]['driver']);
            }

            $tables = [];

            foreach ($modelClasses as $modelClass) {
                $tables[] = $modelClass::tableName();
            }

            ProcessManager::register(make($dispatcher, [
                'connectionName' => $connectionName,
                'tables' => $tables,
                'modelClassResolver' => new TableNameModelClassResolver($modelClasses)
            ]));
        }
    }
}
