<?php

declare(strict_types=1);

namespace Geckoboom\MongoMigrations\Command;

use Geckoboom\Migrations\Blueprint\Command;
use Whirlwind\Infrastructure\Persistence\ConnectionInterface;
use Whirlwind\Persistence\Mongo\MongoConnection;

class DropIndex extends Command
{
    protected string $name;

    /**
     * @param ConnectionInterface&MongoConnection $connection
     * @return void
     */
    public function apply(ConnectionInterface $connection)
    {
        $connection->createCommand(
            $connection->getQueryBuilder()->dropIndexes(
                $this->collection,
                $this->name
            )
        )->execute();
    }
}
