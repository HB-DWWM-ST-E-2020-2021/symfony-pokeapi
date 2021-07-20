<?php

/*
 * This file is part of the symfony-pokeapi package.
 *
 * (c) Benjamin Georgeault
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Command;

use App\PokeAPI\AttackApi;
use App\PokeAPI\PokemonApi;
use App\PokeAPI\TypeApi;

/**
 * Class DataPokemonCommand
 *
 * @author Benjamin Georgeault
 */
class DataPokemonCommand extends AbstractDataCommand
{
    protected static $defaultName = 'app:data-pokemon';
    protected static $defaultDescription = 'Load pokemon data from PokeAPI if your DB is empty.';

    private TypeApi $typeApi;
    private AttackApi $attackApi;

    public function __construct()
    {
        parent::__construct('pokemon');
    }

    /** @required */
    public function setPokemonApi(PokemonApi $pokemonApi): void
    {
        $this->setAbstractApi($pokemonApi);
    }

    /** @required */
    public function setAttackApi(AttackApi $attackApi): void
    {
        $this->attackApi = $attackApi;
    }

    /** @required */
    public function setTypeApi(TypeApi $typeApi): void
    {
        $this->typeApi = $typeApi;
    }

    protected function throwIfCannotLoad(): void
    {
        if (!$this->typeApi->checkIfCollectionIsInitialized()) {
            throw new \RuntimeException('Cannot load "pokemon", you have to load "type" first by running "app:data-type" command.');
        }

        if (!$this->attackApi->checkIfCollectionIsInitialized()) {
            throw new \RuntimeException('Cannot load "pokemon", you have to load "attack" first by running "app:data-attack" command.');
        }
    }
}
