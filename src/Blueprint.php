<?php

declare(strict_types=1);

namespace Geckoboom\MongoMigrations;

use Geckoboom\Migrations\Blueprint as BaseBlueprint;
use Geckoboom\MongoMigrations\Command\CreateCommand;
use Geckoboom\MongoMigrations\Command\CreateIndexCommand;
use Geckoboom\MongoMigrations\Command\DeleteCommand;
use Geckoboom\MongoMigrations\Command\DropCommand;
use Geckoboom\MongoMigrations\Command\DropIndex;
use Geckoboom\MongoMigrations\Command\InsertCommand;
use Geckoboom\MongoMigrations\Command\UpdateCommand;

/**
 * @method Blueprint addCappedOption(bool $isCapped)
 * @method Blueprint addSizeOption(int $size)
 * @method Blueprint addMaxOption(int $max)
 * @method Blueprint addValidationLevelOption(string $level)
 * @method Blueprint addValidationActionOption(string $action)
 * @method Blueprint addCollationOption(\stdClass $collation)
 */
class Blueprint extends BaseBlueprint
{
    protected string $collection;
    protected array $options = [];

    public function create(callable $callback): void
    {
        $this->options = [];
        $callback($this);
        $this->prependCommand(new CreateCommand($this->collection, [
            'options' => $this->options
        ]));
    }

    public function drop(): void
    {
        $this->addCommand(new DropCommand($this->collection));
    }

    public function dropIfExists(): void
    {
        $this->addCommand(new DropCommand($this->collection, ['isExists' => true]));
    }

    public function createIfNotExists(callable $callback): void
    {
        $this->options = [];
        $callback($this);
        $this->prependCommand(new CreateCommand('create', [
            'isNotExists' => true,
            'collection' => $this->collection,
            'options' => $this->options,
        ]));
    }

    public function __call($name, $arguments)
    {
        if (\preg_match('/^add(.*?)Option$/', $name, $matches)) {
            return $this->setOption(\lcfirst($matches[1]), $arguments[0]);
        }

        throw new \BadMethodCallException('Invalid method ' . $name);
    }

    public function setOption(string $name, $value): self
    {
        $this->options[$name] = $value;

        return $this;
    }

    public function createIndex(array $keys, ?string $name = null, array $options = []): void
    {
        $this->addCommand(new CreateIndexCommand(
            $this->collection,
            \compact('keys', 'name', 'options')
        ));
    }

    public function dropIndex(string $name): void
    {
        $this->addCommand(new DropIndex(
            $this->collection,
            \compact('name')
        ));
    }

    public function dropAllIndexes(): void
    {
        $this->addCommand(new DropIndex(
            $this->collection,
            [
                'name' => '*'
            ]
        ));
    }

    public function insert(array $data, array $options = []): void
    {
        $this->addCommand(new InsertCommand(
            $this->collection,
            \compact('data', 'options')
        ));
    }

    public function batchInsert(array $items, array $options = []): void
    {
        $this->addCommand(new InsertCommand(
            $this->collection,
            [
                'data' => $items,
                'isBatch' => true,
                'options' => $options,
            ]
        ));
    }

    public function update(array $conditions, array $document, array $options = []): void
    {
        $this->addCommand(new UpdateCommand(
            $this->collection,
            \compact('conditions', 'document', 'options')
        ));
    }

    public function delete(array $conditions, array $options = []): void
    {
        $this->addCommand(new DeleteCommand(
            $this->collection,
            \compact('conditions', 'options')
        ));
    }
}
