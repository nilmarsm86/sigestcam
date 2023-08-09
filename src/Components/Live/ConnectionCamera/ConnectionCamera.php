<?php
//
//namespace App\Components\Live\ConnectionCamera;
//
//
//use App\DTO\Paginator;
//use App\Entity\Camera;
//use App\Repository\CameraRepository;
//use Doctrine\ORM\AbstractQuery;
//use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;
//use Symfony\UX\LiveComponent\Attribute\LiveAction;
//use Symfony\UX\LiveComponent\Attribute\LiveArg;
//use Symfony\UX\LiveComponent\Attribute\LiveProp;
//use Symfony\UX\LiveComponent\DefaultActionTrait;
//
//#[AsLiveComponent(template: 'components/live/conecction_camera/conecction_camera.html.twig')]
//class ConnectionCamera
//{
//    use DefaultActionTrait;
//
//    #[LiveProp(useSerializerForHydration: true)]
//    public ?Paginator $paginator = null;
//
//    #[LiveProp(writable: true)]
//    public ?string $filter = '';
//
//    #[LiveProp(writable: true)]
//    public ?int $amount = 10;
//
//    #[LiveProp(writable: true)]
//    public bool $isShowDetail = false;
//
//    #[LiveProp(writable: true)]
//    public ?array $data = null;
//
//    #[LiveProp(writable: true)]
//    public ?int $page = 1;
//
//    #[LiveProp(writable: true)]
//    public ?int $fake = null;
//
//    #[LiveProp(writable: true)]
//    public ?string $url = null;
//
//    #[LiveProp]
//    public ?array $camera = null;
//
//    public function __construct(private CameraRepository $cameraRepository)
//    {
//    }
//
//    public function mount(): void
//    {
//        $this->amount = 10;
//        $this->filter = '';
//        $this->page = 1;
//        $data = $this->cameraRepository->findCameras($this->filter, $this->amount, $this->page);
//        $this->paginator = new Paginator($data, $this->amount, $this->page);
//        $this->data = $data->getQuery()->getResult(AbstractQuery::HYDRATE_ARRAY);
//        $this->fake = $data->count();
//        $this->configPaginator();
//    }
//
//    #[LiveAction]
//    public function detail(#[LiveArg] Camera $camera): void
//    {
//        $this->configPaginator();
//        $this->details($camera);
//        $this->isShowDetail = true;
//    }
//
//    private function configPaginator(): void
//    {
//        $this->paginator->setFake($this->fake);
//        $this->paginator->setAmount($this->amount);
//        $this->paginator->setPage($this->page);
//    }
//
//    private function details(Camera $camera): void
//    {
//        $this->camera['id'] = $camera->getId();
//    }
//
//}

namespace App\Components\Live\ConnectionCamera;

//use App\Components\Live\ConnectionCamera\NewCamera;
use App\Components\Live\ConnectionCommutator\CommutatorTable;
use App\Entity\Camera;
use App\Entity\Commutator;
use App\Entity\Enums\ConnectionType;
use App\Entity\Port;
use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;
use Symfony\UX\LiveComponent\Attribute\LiveArg;
use Symfony\UX\LiveComponent\Attribute\LiveListener;
use Symfony\UX\LiveComponent\Attribute\LiveProp;
use Symfony\UX\LiveComponent\DefaultActionTrait;

#[AsLiveComponent(template: 'components/live/connection_camera/connection_camera.html.twig')]
class ConnectionCamera
{
    use DefaultActionTrait;

    #[LiveProp(writable: true)]
    public bool $isShowDetail = false;

    #[LiveProp]
    public ?array $camera = null;

    //#[LiveProp(writable: true)]
    //public ?ConnectionType $connection = null;

//    #[LiveListener(CommutatorDetail::DEACTIVATE_SWITCH)]
//    public function onDeactivateSwitch(#[LiveArg] Commutator $commutator): void
//    {
//        $this->isShowDetail = false;
//        $this->save($commutator);
//    }

    #[LiveListener(NewCamera::FORM_SUCCESS)]
    public function onFormSuccess(): void
    {
        //aactualizar la tabla con el filtro puesto en la camare nueva
        $this->isShowDetail = false;
    }

    #[LiveListener(CameraTable::CHANGE_TABLE)]
    public function onChangeTable(): void
    {
        //aactualizar la tabla con el filtro puesto en el commutator nuevo
        $this->isShowDetail = false;
    }

    private function details(Camera $camera): void
    {
        $this->camera['id'] = $camera->getId();
        //...
    }

    #[LiveListener(CameraTable::SHOW_DETAIL)]
    public function onShowDetail(#[LiveArg] Camera $camera): void
    {
        /*if (isset($this->commutator['id'])) {
            if ($this->commutator['id'] !== $commutator->getId()) {
                $this->details($commutator);
                //$this->portsInfo($commutator);
                $this->isShowDetail = true;
            }
        } else {
            $this->details($commutator);
            //$this->portsInfo($commutator);

            $this->isShowDetail = true;
        }*/
    }

}