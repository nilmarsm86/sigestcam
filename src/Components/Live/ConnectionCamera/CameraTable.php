<?php

namespace App\Components\Live\ConnectionCamera;

use App\Components\Live\Traits\ComponentTable;
use App\Entity\Camera;
use App\Repository\CameraRepository;
use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;
use Symfony\UX\LiveComponent\Attribute\LiveAction;
use Symfony\UX\LiveComponent\Attribute\LiveArg;
use Symfony\UX\LiveComponent\Attribute\LiveListener;
use Symfony\UX\LiveComponent\ComponentToolsTrait;
use Symfony\UX\LiveComponent\DefaultActionTrait;

#[AsLiveComponent(template: 'components/live/connection_camera/camera_table.html.twig')]
class CameraTable
{
    use DefaultActionTrait;
    use ComponentToolsTrait;
    use ComponentTable;

    const CHANGE_TABLE = 'camera_table:change:table';
    const SHOW_DETAIL = 'camera_table:show:detail';

    public function __construct(private readonly CameraRepository $cameraRepository)
    {
    }

    private function reload()
    {
        $data = $this->cameraRepository->findCameras($this->filter, $this->amount, $this->page);
        $this->reloadData($data);
    }

    //ver si lo puedo emitir directamente desde twig
    #[LiveAction]
    public function detail(#[LiveArg] int $cameraId): void
    {
        $this->emit(static::SHOW_DETAIL, [
            'camera' => $cameraId
        ]);
    }

    /**
     * When save new commutator table filer by it
     * @param Camera $camera
     * @return void
     */
    #[LiveListener(NewCamera::FORM_SUCCESS)]
    public function onFormSuccess(#[LiveArg] Camera $camera): void
    {
        $this->filter = $camera->getIp();
        $this->changeFilter();
    }

    /**
     * Get change table event name
     * @return string
     */
    private function getChangeTableEventName(): string
    {
        return static::CHANGE_TABLE;
    }
}