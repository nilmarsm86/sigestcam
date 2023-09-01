<?php

namespace App\Controller;

use App\Entity\Enums\PortType;
use App\Repository\PortRepository;
use App\Service\CrudActionService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/port')]
class PortController extends AbstractController
{
    #[Route('/state', name: 'port_state', methods: ['POST'])]
    public function state(Request $request, PortRepository $portRepository, CrudActionService $crudActionService): Response
    {
        $template = $crudActionService->stateAction($request, $portRepository, 'Se ha desactivado el puerto.', 'Se ha activado el puerto.');
        return new Response($template);
    }

    #[Route('/speed', name: 'port_speed', methods: ['POST'])]
    public function speed(Request $request, PortRepository $portRepository, CrudActionService $crudActionService): Response
    {
        if($request->isXmlHttpRequest()){
            $id = $request->request->get('id');
            $entity = $portRepository->find($id);
            $speed = $request->request->get('speed');
            $entity->configure($speed);

            $portRepository->save($entity, true);
            return $this->render("partials/_form_success.html.twig", [
                'id' => 'speed_'.$speed.'-'.$entity->getId(),
                'type' => 'text-bg-success',
                'message' => 'La velocidad del puerto ha sido modificada.'
            ]);
        }

        throw new BadRequestHttpException('Ajax request');
    }

    #[Route('/type', name: 'port_type', methods: ['POST'])]
    public function type(Request $request, PortRepository $portRepository, CrudActionService $crudActionService): Response
    {
        if($request->isXmlHttpRequest()){
            $id = $request->request->get('id');
            $entity = $portRepository->find($id);

            $typeId = $request->request->get('type');
            $type = PortType::from($typeId);

            $entity->setPortType($type);

            $portRepository->save($entity, true);
            return $this->render("partials/_form_success.html.twig", [
                'id' => 'type_'.$typeId.'-'.$entity->getId(),
                'type' => 'text-bg-success',
                'message' => 'El puerto a sido cambiado a: '.PortType::getLabelFrom($type)
            ]);
        }

        throw new BadRequestHttpException('Ajax request');
    }
}
