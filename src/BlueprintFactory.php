<?php

declare(strict_types=1);

namespace Geckoboom\MongoMigrations;

use Geckoboom\Migrations\BlueprintFactoryInterface;
use Geckoboom\Migrations\BlueprintInterface;

class BlueprintFactory implements BlueprintFactoryInterface
{
    public function create(string $collection): BlueprintInterface
    {
        return new Blueprint($collection);
    }
}
