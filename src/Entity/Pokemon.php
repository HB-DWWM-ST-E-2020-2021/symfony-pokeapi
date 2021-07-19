<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use App\Repository\PokemonRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

/*
 * Entity declared has @ApiResource. It will be available on /api/docs.
 * The default group for normalization is "pokemon:get". You can set the string you want, but it has to be found in @Group
 * annotation on entities.
 */
/**
 * @ApiResource(
 *     normalizationContext={
 *          "groups"={"pokemon:get"}
 *     }
 * )
 * @ORM\Entity(repositoryClass=PokemonRepository::class)
 */
class Pokemon
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     * @ORM\Column(type="integer")
     * @Groups({"pokemon:get"})
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"pokemon:get"})
     */
    private $name;

    /**
     * @ORM\Column(type="integer")
     * @Groups({"pokemon:get"})
     */
    private $height;

    /**
     * @ORM\Column(type="integer")
     * @Groups({"pokemon:get"})
     */
    private $weight;

    /**
     * @ORM\Column(type="integer")
     * @Groups({"pokemon:get"})
     */
    private $baseExperience;

    /**
     * @ORM\Column(type="integer")
     * @Groups({"pokemon:get"})
     */
    private $pokedexOrder;

    /**
     * @ORM\ManyToMany(targetEntity=Type::class, inversedBy="pokemons")
     * @Groups({"pokemon:get"})
     */
    private $types;

    /**
     * @ORM\OneToMany(targetEntity=PokemonAttack::class, mappedBy="pokemon", orphanRemoval=true, cascade={"persist", "remove"})
     * @Groups({"pokemon:get"})
     */
    private $attacks;

    public function __construct(int $id)
    {
        $this->id = $id;
        $this->types = new ArrayCollection();
        $this->attacks = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getHeight(): ?int
    {
        return $this->height;
    }

    public function setHeight(int $height): self
    {
        $this->height = $height;

        return $this;
    }

    public function getWeight(): ?int
    {
        return $this->weight;
    }

    public function setWeight(int $weight): self
    {
        $this->weight = $weight;

        return $this;
    }

    public function getBaseExperience(): ?int
    {
        return $this->baseExperience;
    }

    public function setBaseExperience(int $baseExperience): self
    {
        $this->baseExperience = $baseExperience;

        return $this;
    }

    public function getPokedexOrder(): ?int
    {
        return $this->pokedexOrder;
    }

    public function setPokedexOrder(int $pokedexOrder): self
    {
        $this->pokedexOrder = $pokedexOrder;

        return $this;
    }

    /**
     * @return Collection|Type[]
     */
    public function getTypes(): Collection
    {
        return $this->types;
    }

    public function addType(Type $type): self
    {
        if (!$this->types->contains($type)) {
            $this->types[] = $type;
        }

        return $this;
    }

    public function removeType(Type $type): self
    {
        $this->types->removeElement($type);

        return $this;
    }

    /**
     * @return Collection|PokemonAttack[]
     */
    public function getAttacks(): Collection
    {
        return $this->attacks;
    }

    public function addAttack(PokemonAttack $attack): self
    {
        if (!$this->attacks->contains($attack)) {
            $this->attacks[] = $attack;
        }

        return $this;
    }

    public function removeAttack(PokemonAttack $attack): self
    {
        $this->attacks->removeElement($attack);

        return $this;
    }
}
