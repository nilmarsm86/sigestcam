<?php

namespace App\Entity;

use App\Entity\Enums\Aim;
use App\Entity\Enums\Priority;
use App\Entity\Enums\ReportState;
use App\Entity\Enums\ReportType;
use App\Repository\ReportRepository;
use DateTime;
use DateTimeImmutable;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: ReportRepository::class)]
#[ORM\HasLifecycleCallbacks]
class Report
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
//    #[Assert\NotBlank(message: 'El reporte debe de tener un número.')]
//    #[Assert\NotNull(message: 'El número del reporte no puede ser nulo.')]
//    #[Assert\Positive]
    private ?string $number = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: 'El reporte debe de tener una especialidad.')]
//    #[Assert\NotNull(message: 'La especialidad del reporte no puede ser nulo.')]
    private string $specialty = 'video_vigilancia';

    #[ORM\Column]
//    #[Assert\DateTime]
    private ?DateTimeImmutable $entryDate = null;

    #[ORM\Column(nullable: true)]
//    #[Assert\DateTime]
    private ?DateTimeImmutable $closeDate = null;

    #[ORM\Column(length: 255)]
    private string $type;

    #[Assert\Choice(
        choices: [ReportType::Camera, ReportType::Server, ReportType::Msam, ReportType::Modem, ReportType::Switch],
        message: 'Seleccione un tipo de reporte válido.'
    )]
    private ReportType $enumType;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $interruptionReason = null;

    #[ORM\Column(length: 255)]
    private string $priority;

    #[Assert\Choice(
        choices: [Priority::Hight, Priority::Medium, Priority::Low],
        message: 'Seleccione una prioridad válida.'
    )]
    private Priority $enumPriority;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    #[Assert\Valid]
    private ?Equipment $equipment = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $flaw = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $observation = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $solution = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: 'El reporte debe de tener una unidad.')]
//    #[Assert\NotNull(message: 'la unidad del reporte no puede ser nula.')]
    private string $unit = 'Unidad 1';

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    #[Assert\Valid]
    private User $boss;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    #[Assert\Valid]
    private User $managementOfficer;

    #[ORM\Column(length: 255)]
    private string $state;

    #[Assert\Choice(choices: [ReportState::Open, ReportState::Close], message: 'Seleccione un estado válido.')]
    private ReportState $enumState;

    #[ORM\Column(length: 255)]
    private string $aim;

    #[Assert\Choice(choices: [Aim::NoObjective, Aim::Objective], message: 'Seleccione una función válida.')]
    private Aim $enumAim;

    #[ORM\ManyToOne]
    #[Assert\Valid]
    private ?Organ $organ = null;

    public function __construct()
    {
//        $this->entryDate = new \DateTime();
//        $this->number = $this->entryDate->getTimestamp().'-'.$this->equipment->getShortName();
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

    public function getEntryDate(): ?DateTimeImmutable
    {
        return $this->entryDate;
    }

    public function setEntryDate(DateTimeImmutable $entryDate): static
    {
        $this->entryDate = $entryDate;

        return $this;
    }

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

    public function setType(ReportType $type): static
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

    public function setPriority(Priority $priority): static
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

    public function setUnit(string $unit): static
    {
        $this->unit = $unit;

        return $this;
    }

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

    public function setState(ReportState $state): static
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
    public function beforeSave(): void
    {
        $this->entryDate = new DateTimeImmutable();
        $this->number = $this->entryDate->getTimestamp().'-'.$this->equipment->getShortName();
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
        $this->setState(ReportState::from($this->state));
        $this->setAim(Aim::from($this->aim));
    }

    public function close(): void
    {
        if($this->getSolution()){
            $this->closeDate = new DateTimeImmutable('now');
            $this->setState(ReportState::Close);
        }
    }

    public function isOpen(): bool
    {
        return $this->enumState->value === ReportState::Open->value;
    }

}
