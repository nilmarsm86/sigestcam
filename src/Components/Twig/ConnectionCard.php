<?php

namespace App\Components\Twig;


use App\Entity\Enums\ConnectionType;
use App\Repository\PortRepository;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

#[AsTwigComponent(template: 'components/twig/connection_card.html.twig')]
final class ConnectionCard
{
    public string $title;
    public int $totalAmount;
    public ConnectionType $connection;
    public array $options = [];
    public array $components = [];
    public string $color;

    public function __construct(private readonly PortRepository $portRepository)
    {
    }

    public function mount(ConnectionType $connection)
    {
        $this->color = match ($connection) {
            ConnectionType::Direct => 'primary',
//            ConnectionType::Null => throw new \Exception('To be implemented'),
            ConnectionType::Simple => 'success',
            ConnectionType::SlaveSwitch => 'info',
            ConnectionType::SlaveModem => 'warning',
            ConnectionType::Full => 'danger',
        };
        $this->connection = $connection;
    }

    /**
     * @throws NonUniqueResultException
     * @throws NoResultException
     */
    public function getPercent(): int
    {
        $amountConnections = match ($this->connection) {
            ConnectionType::Direct => $this->portRepository->findAmountDirectConnections(),
//            ConnectionType::Null => throw new \Exception('To be implemented'),
            ConnectionType::Simple => $this->portRepository->findAmountSimpleConnections(),
            ConnectionType::SlaveSwitch => $this->portRepository->findAmountSlaveSwitchConnections(),
            ConnectionType::SlaveModem => $this->portRepository->findAmountSlaveModemConnections(),
            ConnectionType::Full => $this->portRepository->findAmountFullConnections(),
        };

        return round($amountConnections * 100 / $this->totalAmount);
    }

}
