<?php

namespace App\Entity;

use App\Repository\ProvinceRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: ProvinceRepository::class)]
#[Assert\UniqueEntity('name', message: 'La provincia debe ser única.')]
class Province
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\OneToMany(mappedBy: 'province', targetEntity: Municipality::class)]
    private Collection $municipalities;

    public function __construct()
    {
        $this->municipalities = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return Collection<int, Municipality>
     */
    public function getMunicipalities(): Collection
    {
        return $this->municipalities;
    }

    public function addMunicipality(Municipality $municipality): static
    {
        if (!$this->municipalities->contains($municipality)) {
            $this->municipalities->add($municipality);
            $municipality->setProvince($this);
        }

        return $this;
    }

    public function removeMunicipality(Municipality $municipality): static
    {
        if ($this->municipalities->removeElement($municipality)) {
            // set the owning side to null (unless already changed)
            if ($municipality->getProvince() === $this) {
                $municipality->setProvince(null);
            }
        }

        return $this;
    }
}
