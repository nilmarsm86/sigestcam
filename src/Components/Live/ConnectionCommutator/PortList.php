<?php

namespace App\Components\Live\ConnectionCommutator;

use App\Entity\Port;
use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;
use Symfony\UX\LiveComponent\Attribute\LiveArg;
use Symfony\UX\LiveComponent\Attribute\LiveListener;
use Symfony\UX\LiveComponent\Attribute\LiveProp;
use Symfony\UX\LiveComponent\DefaultActionTrait;

#[AsLiveComponent(template: 'components/live/connection_commutator/port_list.html.twig')]
class PortList
{
    use DefaultActionTrait;

    #[LiveProp(updateFromParent: true)]
    public ?array $ports = null;

    #[LiveProp(updateFromParent: true)]
    public ?int $selected = null;
//
//    #[LiveProp]
//    public bool $isEditing = false;
//
//    #[LiveProp]
//    public bool $selected = false;
//
//    #[LiveProp(writable: true )]
//    public ?string $data = null;
//
//    #[LiveAction]
//    public function activateEditing(): void
//    {
//        $this->isEditing = true;
//    }
//
//    #[LiveAction]
//    public function select(): void
//    {
//        $this->selected = true;
//    }
//
//    #[LiveAction]
//    public function activate(PortRepository $portRepository, #[LiveArg] Port $port): void
//    {
//        $port->activate();
//        $portRepository->save($port, true);
//        $this->active = true;
//    }
//
//    #[LiveAction]
//    public function deactivate(PortRepository $portRepository, #[LiveArg] Port $port): void
//    {
//        $port->deactivate();
//        $portRepository->save($port, true);
//        $this->active = false;
//    }
//
//    #[LiveAction]
//    public function save(PortRepository $portRepository, #[LiveArg] Port $port): void
//    {
//        /*$this->errors = $this->validate($validator);
//        $this->validateUniqueIp($commutatorRepository, $commutator);
//
//        if (count($this->errors) === 0) {
//            call_user_func_array([$commutator, $this->setter], [$this->data]);
//            try{
//                $commutatorRepository->save($commutator, true);
//                $this->isEditing = false;
//            }catch (\Exception $exception){
//                $this->errors[] = $exception->getMessage();
//            }
//        }*/
//
//        if(empty($this->data)){
//            $this->data = 1;
//        }
//        $port->configure($this->data);
//        $portRepository->save($port, true);
//        $this->isEditing = false;
//
//    }

    public function mount()
    {
        $this->selected = null;
    }

    #[LiveListener(PortDetail::SELECTED_PORT)]
    public function onSelectedPort(#[LiveArg] Port $port): void
    {
        $this->selected = $port->getId();
    }

}