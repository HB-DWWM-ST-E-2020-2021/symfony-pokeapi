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
 * Class TypeCollectionProvider
 *
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
