<?php

/*
 * This file is part of the symfony-pokeapi package.
 *
 * (c) Benjamin Georgeault
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\PokeAPI;

use App\Entity\Type;
use App\Repository\TypeRepository;

/**
 * Class TypeApi
 *
 * @author Benjamin Georgeault
 *
 * @method Type[]   getCollection(int $offset = 0, int $limit = 50)
 */
class TypeApi extends AbstractApi
{
    private TypeRepository $typeRepository;

    public function __construct(TypeRepository $pokemonRepository)
    {
        parent::__construct('type');
        $this->typeRepository = $pokemonRepository;
    }

    /**
     * @param array $data
     * @return Type
     */
    protected function convertPokeApiToElement(array $data): object
    {
        // Try to find existing type by pokeapi id.
        $type = $this->typeRepository->findOneBy([
            'pokeapiId' => $data['id'],
        ]);

        // If not exist, create it.
        if (null === $type) {
            $type = new Type();
            $type->setName($data['name']);
            $type->setPokeapiId($data['id']);
        }

        return $type;
    }

    public function checkIfCollectionIsInitialized(): bool
    {
        return 0 !== $this->typeRepository->count([]);
    }
}
