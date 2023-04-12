<?php

namespace App\Entity;

use App\Repository\FavouriteFruitRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: FavouriteFruitRepository::class)]
class FavouriteFruit
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(nullable: true)]
    private ?int $fruit_id = null;

    public function getId(): ?int
    {
        return $this->id;
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
}
