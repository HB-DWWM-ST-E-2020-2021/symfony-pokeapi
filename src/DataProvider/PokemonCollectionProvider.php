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

use App\Entity\Pokemon;
use App\PokeAPI\PokemonApi;

/**
 * Class PokemonCollectionProvider
 *
 * @author Benjamin Georgeault
 */
class PokemonCollectionProvider extends AbstractCollectionProvider
{
    /** @required */
    public function setPokemonApi(PokemonApi $pokemonApi): void
    {
        $this->setAbstractApi($pokemonApi);
    }

    public function supports(string $resourceClass, string $operationName = null, array $context = []): bool
    {
        return Pokemon::class === $resourceClass;
    }
}
