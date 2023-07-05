<?php

namespace App\Entity;

use App\Entity\Enums\Aim;
use App\Entity\Enums\Priority;
use App\Entity\Enums\ReportState;
use App\Entity\Enums\ReportType;
use App\Repository\ReportRepository;
use DateTimeImmutable;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ReportRepository::class)]
#[ORM\HasLifecycleCallbacks]
class Report
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private string $number;

    #[ORM\Column(length: 255)]
    private string $specialty = 'video_vigilancia';

    #[ORM\Column]
    private ?DateTimeImmutable $entryDate;

    #[ORM\Column(nullable: true)]
    private ?DateTimeImmutable $closeDate = null;

    #[ORM\Column(length: 255)]
    private string $type;
    private ReportType $enumType;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $interruptionReason = null;

    #[ORM\Column(length: 255)]
    private string $priority;
    private Priority $enumPriority;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: true)]
    private ?Equipment $equipment = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $flaw = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $observation = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $solution = null;

    #[ORM\Column(length: 255)]
    private string $unit = 'Unidad 1';

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private User $boss;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private User $managementOfficer;

    #[ORM\Column(length: 255)]
    private string $state;
    private ReportState $enumState;

    #[ORM\Column(length: 255)]
    private string $aim;
    private Aim $enumAim;

    #[ORM\ManyToOne]
    private ?Organ $organ = null;

    public function __construct()
    {
        $this->entryDate = new DateTimeImmutable('now');
        $this->enumPriority = Priority::Hight;
        $this->enumState = ReportState::Open;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNumber(): string
    {
        return $this->number;
    }

    public function setNumber(string $number): static
    {
        $this->number = $number;

        return $this;
    }

    public function getSpecialty(): string
    {
        return str_replace('_', ' ', $this->specialty);
    }

    /*public function setSpecialty(string $specialty): static
    {
        $this->specialty = $specialty;

        return $this;
    }*/

    public function getEntryDate(): DateTimeImmutable
    {
        return $this->entryDate;
    }

    /*public function setEntryDate(\DateTimeImmutable $entryDate): static
    {
        $this->entryDate = $entryDate;

        return $this;
    }*/

    public function getCloseDate(): ?DateTimeImmutable
    {
        return $this->closeDate;
    }

    public function setCloseDate(?DateTimeImmutable $closeDate): static
    {
        $this->closeDate = $closeDate;

        return $this;
    }

    public function getType(): ReportType
    {
        return $this->enumType;
    }

    private function setType(ReportType $type): static
    {
        $this->enumType = $type;

        return $this;
    }

    public function getInterruptionReason(): ?string
    {
        return $this->interruptionReason;
    }

    public function setInterruptionReason(?string $interruptionReason): static
    {
        $this->interruptionReason = $interruptionReason;

        return $this;
    }

    public function getPriority(): Priority
    {
        return $this->enumPriority;
    }

    private function setPriority(Priority $priority): static
    {
        $this->enumPriority = $priority;

        return $this;
    }

    public function getEquipment(): ?Equipment
    {
        return $this->equipment;
    }

    public function setEquipment(Equipment $equipment): static
    {
        $this->equipment = $equipment;

        return $this;
    }

    public function getFlaw(): ?string
    {
        return $this->flaw;
    }

    public function setFlaw(?string $flaw): static
    {
        $this->flaw = $flaw;

        return $this;
    }

    public function getObservation(): ?string
    {
        return $this->observation;
    }

    public function setObservation(?string $observation): static
    {
        $this->observation = $observation;

        return $this;
    }

    public function getSolution(): ?string
    {
        return $this->solution;
    }

    public function setSolution(?string $solution): static
    {
        $this->solution = $solution;

        return $this;
    }

    public function getUnit(): string
    {
        return $this->unit;
    }

    /*public function setUnit(string $unit): static
    {
        $this->unit = $unit;

        return $this;
    }*/

    public function getBoss(): User
    {
        return $this->boss;
    }

    public function setBoss(User $boss): static
    {
        $this->boss = $boss;

        return $this;
    }

    public function getManagementOfficer(): User
    {
        return $this->managementOfficer;
    }

    public function setManagementOfficer(User $managementOfficer): static
    {
        $this->managementOfficer = $managementOfficer;

        return $this;
    }

    public function getState(): ReportState
    {
        return $this->enumState;
    }

    private function setState(ReportState $state): static
    {
        $this->enumState = $state;

        return $this;
    }

    public function getAim(): Aim
    {
        return $this->enumAim;
    }

    public function setAim(Aim $aim): static
    {
        $this->enumAim = $aim;

        return $this;
    }

    public function getOrgan(): ?Organ
    {
        return $this->organ;
    }

    public function setOrgan(?Organ $organ): static
    {
        $this->organ = $organ;

        return $this;
    }

    public function getPhysicalAddress(): string
    {
        return $this->getEquipment()->getPhysicalAddress();
    }

    public function getMunicipality(): Municipality
    {
        return $this->getEquipment()->getMunicipality();
    }

    #[ORM\PrePersist]
    #[ORM\PreUpdate]
    public function onSave(): void
    {
        $this->type = $this->getType()->value;
        $this->priority = $this->getPriority()->value;
        $this->state = $this->getState()->value;
        $this->aim = $this->getAim()->value;
    }

    #[ORM\PostLoad]
    public function onLoad(): void
    {
        $this->setType(ReportType::from($this->type));
        $this->setPriority(Priority::from($this->priority));
        $this->setState(ReportState::from($this->organ));
        $this->setAim(Aim::from($this->aim));
    }

}
