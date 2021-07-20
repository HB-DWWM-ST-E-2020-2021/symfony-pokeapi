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
use App\PokeAPI\TypeApi;

/**
 * Class DataAttackCommand
 *
 * @author Benjamin Georgeault
 */
class DataAttackCommand extends AbstractDataCommand
{
    protected static $defaultName = 'app:data-attack';
    protected static $defaultDescription = 'Load attacks data from PokeAPI if your DB is empty.';

    private TypeApi $typeApi;

    public function __construct()
    {
        parent::__construct('attack');
    }

    /** @required */
    public function setAttackApi(AttackApi $attackApi): void
    {
        $this->setAbstractApi($attackApi);
    }

    /** @required */
    public function setTypeApi(TypeApi $typeApi): void
    {
        $this->typeApi = $typeApi;
    }

    protected function throwIfCannotLoad(): void
    {
        if (!$this->typeApi->checkIfCollectionIsInitialized()) {
            throw new \RuntimeException('Cannot load "attack", you have to load "type" first by running "app:data-type" command.');
        }
    }
}
