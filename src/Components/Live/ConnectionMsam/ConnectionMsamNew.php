<?php

namespace App\Components\Live\ConnectionMsam;

use App\Components\Live\ConnectionCommutator\ConnectionCommutatorTable;
use App\Components\Live\Traits\ComponentNewForm;
use App\Entity\Enums\ConnectionType;
use App\Entity\Msam;
use App\Entity\Port;
use App\Form\MsamType;
use App\Repository\MsamRepository;
use App\Repository\MunicipalityRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormInterface;
use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;
use Symfony\UX\LiveComponent\Attribute\LiveAction;
use Symfony\UX\LiveComponent\Attribute\LiveListener;
use Symfony\UX\LiveComponent\Attribute\LiveProp;
use Symfony\UX\LiveComponent\ComponentToolsTrait;
use Symfony\UX\LiveComponent\ComponentWithFormTrait;
use Symfony\UX\LiveComponent\DefaultActionTrait;

#[AsLiveComponent(template: 'components/live/connection_msam/new.html.twig')]
class ConnectionMsamNew extends AbstractController
{
    use DefaultActionTrait;
    use ComponentWithFormTrait;
    use ComponentToolsTrait;
    use ComponentNewForm;

    const FORM_SUCCESS = self::class.'_form_success';
    const MODAL_CLOSE = 'modal_form_close';

    #[LiveProp]
    public ?Msam $sam = null;

    #[LiveProp]
    public ?Port $port = null;

    #[LiveProp(writable: true)]
    public ?string $province = null;

    #[LiveProp(writable: true)]
    public ?string $municipality = null;

    #[LiveProp]
    public ?ConnectionType $connection = null;

    protected function instantiateForm(): FormInterface
    {
        $options = [
            'province' => (int) $this->province,
            'municipality' => (int) $this->municipality
        ];

        return $this->createForm(MsamType::class, $this->sam, $options);
    }

    #[LiveListener(ConnectionCommutatorTable::CHANGE.'_Full')]
    public function onConnectionCommutatorTableChangeDirect(): void
    {
        $this->port = null;
        $this->sam = null;
    }

    #[LiveAction]
    public function save(MsamRepository $msamRepository, MunicipalityRepository $municipalityRepository)
    {
        $this->submitForm();

        if($this->isSubmitAndValid()){
            //lanzar evento a JS
            $this->dispatchBrowserEvent(static::MODAL_CLOSE);
            $msam = $this->mapped($municipalityRepository, $this->getForm()->getData());
            $msamRepository->save($msam, true);

            $this->emitSuccess([
                'msam' => $msam->getId(),
            ]);
        }
    }

    protected function mapped(MunicipalityRepository $municipalityRepository, Msam $msam): Msam
    {
        return $msam->setMunicipality($municipalityRepository->find($this->municipality));
    }

    /**
     * Get form success event name
     * @return string
     */
    protected function getSuccessFormEventName(): string
    {
        return static::FORM_SUCCESS.'_'.$this->connection->name;
    }
}