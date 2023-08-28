<?php

namespace App\Components\Twig;


use App\Entity\Enums\ConnectionType;
use App\Repository\PortRepository;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use Exception;
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
    public $amount = null;

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
     * @throws Exception
     */
    public function getPercent(): int
    {
        $amountConnections = match ($this->connection) {
            ConnectionType::Direct => (!is_null($this->amount)) ? $this->amount : $this->portRepository->findAmountDirectConnections(),
            ConnectionType::Simple => (!is_null($this->amount)) ? $this->amount : $this->portRepository->findAmountSimpleConnections(),
            ConnectionType::SlaveSwitch => (!is_null($this->amount)) ? $this->amount : $this->portRepository->findAmountSlaveSwitchConnections(),
            ConnectionType::SlaveModem => (!is_null($this->amount)) ? $this->amount : $this->portRepository->findAmountSlaveModemConnections(),
            ConnectionType::Full => (!is_null($this->amount)) ? $this->amount : $this->portRepository->findAmountFullConnections(),
            ConnectionType::Null => throw new Exception('To be implemented'),
        };

        return round($amountConnections * 100 / $this->totalAmount);
    }

}
