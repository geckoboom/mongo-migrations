<?php

declare(strict_types=1);

namespace Geckoboom\MongoMigrations\Command;

use Geckoboom\Migrations\Blueprint\Command;
use Whirlwind\Infrastructure\Persistence\ConnectionInterface;
use Whirlwind\Persistence\Mongo\MongoConnection;

class DeleteCommand extends Command
{
    protected array $conditions;
    protected array $options = [];

    /**
     * @param ConnectionInterface&MongoConnection $connection
     * @return void
     */
    public function apply(ConnectionInterface $connection)
    {
        $connection->createCommand()->delete(
            $this->collection,
            $this->conditions,
            $this->options
        );
    }
}
