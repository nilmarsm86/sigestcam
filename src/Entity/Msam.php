<?php

namespace App\Entity;

use App\Entity\Enums\State;
use App\Repository\MsamRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Exception;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: MsamRepository::class)]
class Msam extends Equipment
{
    #[ORM\OneToMany(mappedBy: 'msam', targetEntity: Card::class)]
    #[ORM\OrderBy(['slot' => 'ASC'])]
    private Collection $cards;

    #[ORM\Column]
    #[Assert\Positive]
    private ?int $slotAmount = null;

    public function __construct()
    {
        parent::__construct();
        /*$this->inventory = null;
        $this->physicalSerial = null;
        $this->contic = null;*/
        $this->cards = new ArrayCollection();
        $this->enumState = State::Active;
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
        if($this->cards->count() === $this->slotAmount){
            throw new Exception('Ha alcanzado el número máximo de slots de targetas permitidos para este Msam.');
        }

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

            //TODO: de la targeta eliminada debo eliminar tambien los puertos
            //TODO: de los puertos eliminados debo desconectar los equipos conectados al mismo
        }

        return $this;
    }

    public function getSlotAmount(): ?int
    {
        return $this->slotAmount;
    }

    public function setSlotAmount(int $slotAmount): static
    {
        $this->slotAmount = $slotAmount;

        return $this;
    }

    /**
     * Deactivate
     * @return $this
     */
    public function deactivate(): static
    {
        foreach ($this->getCards() as $card){
            /** @var Card $card */
            $card->deactivate();
        }
        return parent::deactivate();
    }

    public function __toString(): string
    {
        $namespace = explode('\\', static::class);
        $data = $namespace[count($namespace) - 1];
        if(!is_null($this->getPhysicalSerial())){
            $data .= ': ('.$this->getPhysicalSerial().')';
        }

        return $data;
    }

    public function canAddTarget(): bool
    {
        return $this->getCards()->count() < $this->slotAmount;
    }

    public function connectedCards()
    {
        return $this->getCards()->count();
    }

}
