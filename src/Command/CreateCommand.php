<?php

declare(strict_types=1);

namespace Geckoboom\MongoMigrations\Command;

use Geckoboom\Migrations\Blueprint\Command;
use Whirlwind\Infrastructure\Persistence\ConnectionInterface;
use Whirlwind\Persistence\Mongo\MongoConnection;

class CreateCommand extends Command
{
    protected bool $isNotExists = false;
    protected array $options = [];

    /**
     * @param ConnectionInterface&MongoConnection $connection
     * @return void
     */
    public function apply(ConnectionInterface $connection)
    {
        if ($this->isNotExists) {
            $document = $connection->getQueryBuilder()->listCollections(['name' => $this->collection]);

            if ($connection->createCommand($document)->execute()->toArray()) {
                return;
            }
        }
        $document = $connection->getQueryBuilder()->createCollection($this->collection, $this->options);
        $connection->createCommand($document)->execute();
    }
}
