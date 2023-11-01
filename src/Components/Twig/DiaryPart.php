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

    public function ripCamera()
    {
        return $this->reportRepository->findInterruptionAndEquipment(InterruptionReason::Review);
    }

    public function connectivityCamera()
    {
        return $this->reportRepository->findInterruptionAndEquipment(InterruptionReason::Connectivity);
    }

//    public function connectivityModem()
//    {
//        return $this->reportRepository->findInterruptionAndEquipment(ReportType::Modem, InterruptionReason::Connectivity);
//    }

    public function electricFluidCamera()
    {
        return $this->reportRepository->findInterruptionAndEquipment(InterruptionReason::ElectricFluid);
    }

    public function sustitutionCamera()
    {
        return $this->reportRepository->findInterruptionAndEquipment(InterruptionReason::Camera, ReportType::Camera);
    }

    public function sustitutionModem()
    {
        return $this->reportRepository->findInterruptionAndEquipment(InterruptionReason::Modem, ReportType::Modem);
    }

//    public function others()
//    {
//        return $this->reportRepository->findInterruption();
//    }

    public function resolvedInterruption()
    {
        return $this->reportRepository->findResolvedInterruption();
    }

}
