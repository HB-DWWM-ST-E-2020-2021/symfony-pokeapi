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

use App\Entity\Attack;
use App\Repository\AttackRepository;
use App\Repository\TypeRepository;

/**
 * Class AttackApi
 *
 * @author Benjamin Georgeault
 *
 * @method Attack[] getCollection(int $offset = 0, int $limit = 50)
 */
class AttackApi extends AbstractApi
{
    private AttackRepository $attackRepository;
    private TypeRepository $typeRepository;

    public function __construct(AttackRepository $attackRepository, TypeRepository $typeRepository)
    {
        parent::__construct('move');
        $this->attackRepository = $attackRepository;
        $this->typeRepository = $typeRepository;
    }

    /**
     * @param array $data
     * @return Attack
     */
    protected function convertPokeApiToElement(array $data): object
    {
        // Try to find existing attack by pokeapi id.
        $attack = $this->attackRepository->findOneBy([
            'pokeapiId' => $data['id'],
        ]);

        // If not exist, create it.
        if (null === $attack) {
            $attack = new Attack();
            $attack->setName($data['name']);
            $attack->setPokeapiId($data['id']);
            $attack->setAccuracy($data['accuracy'] ?? 0);
            $attack->setPower($data['power'] ?? 0);
            $attack->setPp($data['pp'] ?? 0);
            $attack->setType(
                $this->typeRepository->findOneBy([
                    'name' => $data['type']['name'],
                ])
            );
        }

        return $attack;
    }

    public function checkIfCollectionIsInitialized(): bool
    {
        return 0 !== $this->attackRepository->count([]);
    }
}
