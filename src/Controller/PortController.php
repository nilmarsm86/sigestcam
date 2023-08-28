<?php

namespace App\Controller;

use App\Repository\CommutatorRepository;
use App\Repository\PortRepository;
use App\Service\CrudActionService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
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
}
