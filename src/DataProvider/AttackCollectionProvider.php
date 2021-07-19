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

use App\Entity\Attack;
use App\PokeAPI\AttackApi;

/**
 * Class AttackCollectionProvider
 *
 * @author Benjamin Georgeault
 */
class AttackCollectionProvider extends AbstractCollectionProvider
{
    /** @required */
    public function setAttackApi(AttackApi $attackApi): void
    {
        $this->setAbstractApi($attackApi);
    }

    public function supports(string $resourceClass, string $operationName = null, array $context = []): bool
    {
        return Attack::class === $resourceClass;
    }
}
