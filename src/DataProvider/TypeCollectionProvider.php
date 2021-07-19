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

use App\Entity\Type;
use App\PokeAPI\TypeApi;

/**
 * @author Benjamin Georgeault
 */
class TypeCollectionProvider extends AbstractCollectionProvider
{
    /** @required */
    public function setTypeApi(TypeApi $typeApi): void
    {
        $this->setAbstractApi($typeApi);
    }

    public function supports(string $resourceClass, string $operationName = null, array $context = []): bool
    {
        return Type::class === $resourceClass;
    }
}
