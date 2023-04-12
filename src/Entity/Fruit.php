<?php

namespace App\Entity;

use App\Repository\FruitRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: FruitRepository::class)]
class Fruit
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $name = null;

    #[ORM\Column(length: 11, nullable: true)]
    private ?int $fruit_id = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $family = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $fruit_order = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $genus = null;

    #[ORM\Column(nullable: true)]
    private array $nutritions = [];

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(?string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getFruitId(): ?int
    {
        return $this->fruit_id;
    }

    public function setFruitId(?int $fruit_id): self
    {
        $this->fruit_id = $fruit_id;

        return $this;
    }

    public function getFamily(): ?string
    {
        return $this->family;
    }

    public function setFamily(?string $family): self
    {
        $this->family = $family;

        return $this;
    }

    public function getFruitOrder(): ?string
    {
        return $this->fruit_order;
    }

    public function setFruitOrder(?string $fruit_order): self
    {
        $this->fruit_order = $fruit_order;

        return $this;
    }

    public function getGenus(): ?string
    {
        return $this->genus;
    }

    public function setGenus(?string $genus): self
    {
        $this->genus = $genus;

        return $this;
    }

    public function getNutritions(): array
    {
        return $this->nutritions;
    }

    public function setNutritions(?array $nutritions): self
    {
        $this->nutritions = $nutritions;

        return $this;
    }
}
