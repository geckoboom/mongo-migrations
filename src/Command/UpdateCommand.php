<?php

declare(strict_types=1);

namespace Geckoboom\MongoMigrations\Command;

use Geckoboom\Migrations\Blueprint\Command;
use Whirlwind\Infrastructure\Persistence\ConnectionInterface;
use Whirlwind\Persistence\Mongo\MongoConnection;

class UpdateCommand extends Command
{
    protected array $conditions;
    protected array $document;
    protected array $options = [];
    /**
     * @param ConnectionInterface&MongoConnection $connection
     * @return void
     */
    public function apply(ConnectionInterface $connection)
    {
        $connection->createCommand()->update(
            $this->collection,
            $this->conditions,
            $this->document,
            $this->options
        );
    }
}
