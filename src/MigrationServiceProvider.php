<?php

declare(strict_types=1);

namespace Geckoboom\MongoMigrations;

use Geckoboom\Migrations\BlueprintFactoryInterface;
use Geckoboom\Migrations\Command\Migration\CreateCommand;
use Geckoboom\Migrations\Command\Migration\InstallCommand;
use Geckoboom\Migrations\Command\Migration\RollbackCommand;
use Geckoboom\Migrations\Command\Migration\StatusCommand;
use Geckoboom\Migrations\Config\Config;
use Geckoboom\Migrations\Domain\Migration;
use Geckoboom\Migrations\Domain\MigrationRepositoryInterface;
use Geckoboom\Migrations\Infrastructure\Repository\MigrationRepository;
use Geckoboom\Migrations\Infrastructure\Repository\MigrationTableGatewayInterface;
use League\Container\ServiceProvider\AbstractServiceProvider;
use League\Container\ServiceProvider\BootableServiceProviderInterface;
use Whirlwind\App\Console\Application;
use Whirlwind\Infrastructure\Hydrator\Hydrator;
use Whirlwind\Infrastructure\Persistence\ConnectionInterface;
use Whirlwind\Infrastructure\Repository\ResultFactory;
use Whirlwind\Persistence\Mongo\ConditionBuilder\ConditionBuilder;
use Whirlwind\Persistence\Mongo\MongoConnection;
use Whirlwind\Persistence\Mongo\Query\MongoQueryFactory;

class MigrationServiceProvider extends AbstractServiceProvider implements BootableServiceProviderInterface
{
    public function provides(string $id): bool
    {
        return \in_array(
            $id,
            [
                MigrationTableGatewayInterface::class,
                MigrationRepositoryInterface::class,
                BlueprintFactoryInterface::class,
                ConnectionInterface::class,
            ]
        );
    }

    public function register(): void
    {
        $this->getContainer()->add(
            MigrationTableGateway::class,
            fn (): MigrationTableGatewayInterface => new MigrationTableGateway(
                $this->getContainer()->get(MongoConnection::class),
                $this->getContainer()->get(MongoQueryFactory::class),
                $this->getContainer()->get(ConditionBuilder::class),
                $this->getContainer()->get(Config::class)->getCollectionName()
            )
        );

        $this->getContainer()->add(
            MigrationRepositoryInterface::class,
            fn (): MigrationRepositoryInterface => $this->getContainer()->get(MigrationRepository::class)
        );

        $this->getContainer()->add(
            MigrationRepository::class,
            fn (): MigrationRepository => new MigrationRepository(
                $this->getContainer()->get(MigrationTableGatewayInterface::class),
                $this->getContainer()->get(Hydrator::class),
                Migration::class,
                $this->getContainer()->get(ResultFactory::class)
            )
        );

        $this->getContainer()->add(
            BlueprintFactoryInterface::class,
            fn (): BlueprintFactoryInterface => new BlueprintFactory()
        );

        $this->getContainer()->add(
            ConnectionInterface::class,
            fn (): ConnectionInterface => $this->getContainer()->get(MongoConnection::class)
        );
    }

    public function boot(): void
    {
        /** @var Application $app */
        $app = $this->getContainer()->get(Application::class);
        $app->addCommand('migrate:create', CreateCommand::class);
        $app->addCommand('migrate:install', InstallCommand::class);
        $app->addCommand('migrate:rollback', RollbackCommand::class);
        $app->addCommand('migrate:status', StatusCommand::class);
    }
}
