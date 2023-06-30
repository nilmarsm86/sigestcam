<?php

namespace App\Entity;

use App\Repository\MsamRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: MsamRepository::class)]
class Msam extends ConnectedElement
{
    #[ORM\OneToMany(mappedBy: 'msam', targetEntity: Card::class)]
    #[ORM\OrderBy(['slot' => 'ASC'])]
    private Collection $cards;

    public function __construct()
    {
        parent::__construct();
        $this->cards = new ArrayCollection();
    }

    /**
     * @return Collection<int, Card>
     */
    public function getCards(): Collection
    {
        return $this->cards;
    }

    public function addCard(Card $card): static
    {
        if (!$this->cards->contains($card)) {
            $this->cards->add($card);
            $card->setMsam($this);
        }

        return $this;
    }

    public function removeCard(Card $card): static
    {
        if ($this->cards->removeElement($card)) {
            // set the owning side to null (unless already changed)
            if ($card->getMsam() === $this) {
                $card->setMsam(null);
            }
        }

        return $this;
    }

}
