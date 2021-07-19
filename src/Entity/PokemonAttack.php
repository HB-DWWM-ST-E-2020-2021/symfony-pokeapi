<?php

namespace App\Entity;

use App\Repository\PokemonAttackRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * This class has a composite primary key. 2 attributes (pokemon and attack) are used to create its primary key (@ORM\Id).
 * @see https://www.javatpoint.com/sql-composite-key
 * @see https://www.doctrine-project.org/projects/doctrine-orm/en/2.9/tutorials/composite-primary-keys.html
 *
 * @ORM\Entity(repositoryClass=PokemonAttackRepository::class)
 */
class PokemonAttack
{
    /**
     * @ORM\Id
     * @ORM\ManyToOne(targetEntity=Pokemon::class, inversedBy="attacks")
     * @ORM\JoinColumn(nullable=false)
     */
    private $pokemon;

    /**
     * @ORM\Id
     * @ORM\ManyToOne(targetEntity=Attack::class, inversedBy="pokemons")
     * @ORM\JoinColumn(nullable=false)
     * @Groups({"pokemon:get"})
     */
    private $attack;

    /**
     * @ORM\Column(type="integer")
     * @Groups({"pokemon:get"})
     */
    private $level;

    public function __construct(Pokemon $pokemon, Attack $attack)
    {
        $this->pokemon = $pokemon;
        $this->attack = $attack;
        $pokemon->addAttack($this);
        $attack->addPokemon($this);
    }

    public function getPokemon(): ?Pokemon
    {
        return $this->pokemon;
    }

    public function getAttack(): ?Attack
    {
        return $this->attack;
    }

    public function getLevel(): ?int
    {
        return $this->level;
    }

    public function setLevel(int $level): self
    {
        $this->level = $level;

        return $this;
    }
}
