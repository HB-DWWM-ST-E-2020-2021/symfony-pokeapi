<?php

/*
 * This file is part of the symfony-pokeapi package.
 *
 * (c) Benjamin Georgeault
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\DataProvider;

use ApiPlatform\Core\Bridge\Doctrine\Orm\CollectionDataProvider;
use App\Entity\Type;
use App\Pokedex\TypeApi;
use Doctrine\Persistence\ManagerRegistry;

/**
 * DataProvider are used to load data from source. By default, API platform use your database as source.
 * In our case, we want to populate database with data from PokeAPI before using DB to generate JSON.
 *
 * @see https://api-platform.com/docs/core/data-providers/
 * @author Benjamin Georgeault
 */
class TypeCollectionProvider extends CollectionDataProvider
{
    private TypeApi $typeApi;

    public function __construct(TypeApi $typeApi, ManagerRegistry $managerRegistry, iterable $collectionExtensions = [])
    {
        parent::__construct($managerRegistry, $collectionExtensions);
        $this->typeApi = $typeApi;
    }

    public function supports(string $resourceClass, string $operationName = null, array $context = []): bool
    {
        return Type::class === $resourceClass;
    }

    public function getCollection(string $resourceClass, string $operationName = null, array $context = [])
    {
        $this->typeApi->getTypes();

        return parent::getCollection($resourceClass, $operationName, $context);
    }
}
