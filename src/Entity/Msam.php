<?php

namespace App\Entity;

use App\Entity\Enums\State as StateEnum;
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
    #[Assert\Count(
        min: 1,
        minMessage: 'Debe establecer al menos 1 targeta para este Msam.',
    )]
    private Collection $cards;

    #[ORM\Column]
    #[Assert\Positive]
    private ?int $slotAmount = null;

    public function __construct()
    {
        parent::__construct();
        /*$this->inventory = null;
        $this->physicalSerial = null;
        $this->contic = null;
        $this->ip = null;*/
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

            //de la targeta eliminada debo eliminar tambien los puertos
            //de los puertos eliminados debo desconectar los equipos conectados al mismo
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
     * @throws Exception
     */
    public function getIp(): ?string
    {
        throw new Exception('Msam no tiene Ip');
    }

    /**
     * @throws Exception
     */
    public function setIp(?string $ip): static
    {
        throw new Exception('Msam no tiene Ip');
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

}
