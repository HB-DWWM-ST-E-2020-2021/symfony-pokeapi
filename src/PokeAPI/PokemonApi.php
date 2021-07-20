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

use App\Entity\Pokemon;
use App\Entity\PokemonAttack;
use App\Repository\AttackRepository;
use App\Repository\PokemonRepository;
use App\Repository\TypeRepository;

/**
 * Class PokemonApi
 *
 * @author Benjamin Georgeault
 */
class PokemonApi extends AbstractApi
{
    private PokemonRepository $pokemonRepository;
    private TypeRepository $typeRepository;
    private AttackRepository $attackRepository;

    public function __construct(PokemonRepository $pokemonRepository, TypeRepository $typeRepository, AttackRepository $attackRepository)
    {
        parent::__construct('pokemon');
        $this->pokemonRepository = $pokemonRepository;
        $this->typeRepository = $typeRepository;
        $this->attackRepository = $attackRepository;
    }

    /**
     * @param array $data
     * @return Pokemon
     */
    public function convertPokeApiToElement(array $data): object
    {
        // Try to find existing pokemon by pokeapi id.
        $pokemon = $this->pokemonRepository->find($data['id']);

        // If not exist, create it.
        if (null === $pokemon) {
            $pokemon = new Pokemon($data['id']);
            $pokemon->setName($data['name']);
            $pokemon->setBaseExperience($data['base_experience'] ?? 0);
            $pokemon->setHeight($data['height'] ?? 0);
            $pokemon->setWeight($data['weight'] ?? 0);
            $pokemon->setPokedexOrder($data['pokedex_order'] ?? 0);

            foreach ($data['types'] as $type) {
                $pokemon->addType(
                    $this->typeRepository->findOneBy([
                        'name' => $type['type']['name'],
                    ])
                );
            }

            $i = 0;
            foreach ($data['moves'] as $move) {
                if (1 < $i) {
                    // Limit to 2 moves only, too much data load at one time.
                    break;
                }

                $attack = $this->attackRepository->findOneBy([
                    'name' => $move['move']['name'],
                ]);

                $pokemonAttack = new PokemonAttack($pokemon, $attack);
                $pokemonAttack->setLevel($move['version_group_details'][0]['level_learned_at']);
                $i++;
            }
        }

        return $pokemon;
    }

    public function checkIfCollectionIsInitialized(): bool
    {
        return 0 !== $this->pokemonRepository->count([]);
    }
}
