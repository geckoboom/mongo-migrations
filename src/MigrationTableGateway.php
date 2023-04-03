<?php

declare(strict_types=1);

namespace Geckoboom\MongoMigrations;

use Geckoboom\Migrations\Infrastructure\Repository\MigrationTableGatewayInterface;
use Whirlwind\Persistence\Mongo\MongoConnection;
use Whirlwind\Persistence\Mongo\MongoTableGateway;

/**
 * @property MongoConnection $connection
 */
class MigrationTableGateway extends MongoTableGateway implements MigrationTableGatewayInterface
{
    public function queryOrCreateCollection(array $conditions = [], int $limit = 0, array $order = []): array
    {
        $document = $this->connection->getQueryBuilder()->listCollections(['name' => $this->collectionName]);

        if (!$this->connection->createCommand($document)->execute()->toArray()) {
            $this->createMigrationCollection();
        }


        return $this->queryAll($conditions, $order, $limit);
    }

    protected function createMigrationCollection(): void
    {
        $this->connection->createCommand($this->connection->getQueryBuilder()->createCollection($this->collectionName));

        $indexes = [
            [
                'key' => ['name' => -1],
                'name' => 'name_idx',
                'unique' => true,
            ],
            [
                'key' => ['createdAt' => -1],
                'name' => 'created_idx',
            ]
        ];
        $this->connection->createCommand($this->connection->getQueryBuilder()->createIndexes(
            $this->connection->getDefaultDatabaseName(),
            $this->collectionName,
            $indexes
        ));
    }
}
