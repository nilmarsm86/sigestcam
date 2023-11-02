<?php

namespace App\Components\Twig;

use App\Entity\Enums\InterruptionReason;
use App\Entity\Enums\ReportType;
use App\Repository\ReportRepository;
use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

#[AsTwigComponent(template: 'components/twig/diary_part.html.twig')]
final class DiaryPart
{
    public function __construct(private readonly ReportRepository $reportRepository)
    {
    }

    /**
     * @return bool|float|int|string|null
     */
    public function ripCamera()
    {
        return $this->reportRepository->findInterruptionAndEquipment(InterruptionReason::Review);
    }

    /**
     * @return bool|float|int|string|null
     */
    public function connectivityCamera()
    {
        return $this->reportRepository->findInterruptionAndEquipment(InterruptionReason::Connectivity);
    }

    /**
     * @return bool|float|int|string|null
     */
    public function electricFluidCamera()
    {
        return $this->reportRepository->findInterruptionAndEquipment(InterruptionReason::ElectricFluid);
    }

    /**
     * @return bool|float|int|string|null
     */
    public function sustitutionCamera()
    {
        return $this->reportRepository->findInterruptionAndEquipment(InterruptionReason::Camera, ReportType::Camera);
    }

    /**
     * @return bool|float|int|string|null
     */
    public function sustitutionModem()
    {
        return $this->reportRepository->findInterruptionAndEquipment(InterruptionReason::Modem, ReportType::Modem);
    }

    /**
     * @return bool|float|int|string|null
     */
    public function resolvedInterruption()
    {
        return $this->reportRepository->findResolvedInterruption();
    }

}
