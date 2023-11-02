<?php

namespace App\Components\Twig;

use App\Entity\Enums\ReportType;
use App\Repository\ReportRepository;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use Exception;
use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

#[AsTwigComponent(template: 'components/twig/report_item.html.twig')]
final class ReportItem
{
    public string $date;
    public string $title;
    public string $icon;
    public string $color;
    public ReportType $reportType;

    public function __construct(private readonly ReportRepository $reportRepository)
    {
    }

    public function mount(ReportType $reportType): void
    {
        $this->color = match ($reportType) {
            ReportType::Modem => 'primary',
            ReportType::Camera => 'success',
            ReportType::Switch => 'info',
            ReportType::Msam => 'warning',
            ReportType::Server => 'dark',
        };
        $this->reportType = $reportType;
    }

    /**
     * @throws NonUniqueResultException
     * @throws NoResultException
     * @throws Exception
     */
    public function getAmount(): int
    {
        return match ($this->reportType) {
            ReportType::Modem => $this->reportRepository->findOpenAmountReports(ReportType::Modem),
            ReportType::Camera => $this->reportRepository->findOpenAmountReports(ReportType::Camera),
            ReportType::Switch => $this->reportRepository->findOpenAmountReports(ReportType::Switch),
            ReportType::Msam => $this->reportRepository->findOpenAmountReports(ReportType::Msam),
            ReportType::Server => $this->reportRepository->findOpenAmountReports(ReportType::Server),
            ReportType::Null => throw new Exception('To be implemented'),
        };
    }

}
