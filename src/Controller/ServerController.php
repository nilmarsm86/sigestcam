<?php

namespace App\Controller;

use App\Controller\Traits\MunicipalityTrait;
use App\Entity\Server;
use App\Form\ServerType;
use App\Repository\CameraRepository;
use App\Repository\ServerRepository;
use App\Service\CrudActionService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/server')]
class ServerController extends AbstractController
{
    use MunicipalityTrait;

    #[Route('/', name: 'server_index', methods: ['GET'])]
    #[IsGranted('ROLE_OFFICER')]
    public function index(Request $request, ServerRepository $serverRepository, CrudActionService $crudActionService): Response
    {
        $template = $crudActionService->indexAction($request, $serverRepository, 'findServers', 'server');
        return new Response($template);
    }

    #[Route('/new', name: 'server_new', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_ADMIN')]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $server = new Server();
        $form = $this->createForm(ServerType::class, $server, [
            'action' => $this->generateUrl('server_new'),
            'crud' => true
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $dataAddress = $request->request->all()['server']['address'];
            $municipalityId = $dataAddress['municipality'] ?? null;
            $municipality = $this->findMunicipalityForEquipment($entityManager, $municipalityId);

            $server->setMunicipality($municipality);
            $server->deactivate();

            $entityManager->persist($server);
            $entityManager->flush();

            //si el formulario se mando correctamente por ajax
            if($request->isXmlHttpRequest()){
                return $this->render("partials/_form_success.html.twig", [
                    'id' => 'new_server_'.$server->getId(),
                    'type' => 'text-bg-success',
                    'message' => 'Se ha agregado un servidor.'
                ]);
            }

            $this->addFlash('success', 'Se ha agregado un servidor.');
            return $this->redirectToRoute('server_index', [], Response::HTTP_SEE_OTHER);
        }

        $template = ($request->isXmlHttpRequest()) ? '_form.html.twig' : 'new.html.twig';

        return $this->render("server/$template", [
            'server' => $server,
            'form' => $form,
            'title' => 'Nuevo servidor'
        ]);
    }

    #[Route('/{id}', name: 'server_show', methods: ['GET'])]
    #[IsGranted('ROLE_ADMIN')]
    public function show(Request $request,Server $server, CrudActionService $crudActionService): Response
    {
        $template = $crudActionService->showAction($request, $server, 'server', 'server', 'Detalles del servidor');
        return new Response($template);
    }

    #[Route('/{id}/edit', name: 'server_edit', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_ADMIN')]
    public function edit(Request $request, Server $server, EntityManagerInterface $entityManager): Response
    {
        $postData = $request->request->all();

        $provinceId = isset($postData['server'])
            ? $request->request->all()['server']['address']['province']
            : $server->getMunicipality()->getProvince()->getId();

        $municipalityId = $this->findMunicipalityForExistEquipment($server, $request, 'server');

        $form = $this->createForm(ServerType::class, $server, [
            'action' => $this->generateUrl('server_edit', ['id' => $server->getId()]),
            'province' => (int) $provinceId,
            'municipality' => (int) $municipalityId,
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $municipality = $this->findMunicipalityForEquipment($entityManager, $municipalityId);
            $server->setMunicipality($municipality);
            $entityManager->flush();

            //si el formulario se mando correctamente por ajax
            if($request->isXmlHttpRequest()){
                return $this->render("partials/_form_success.html.twig", [
                    'id' => 'edit_server_'.$server->getId(),
                    'type' => 'text-bg-success',
                    'message' => 'Se ha modificado el servidor.'
                ]);
            }

            $this->addFlash('success', 'Se ha modificado el servidor.');
            return $this->redirectToRoute('server_index', [], Response::HTTP_SEE_OTHER);
        }

        $template = ($request->isXmlHttpRequest()) ? '_form.html.twig' : 'edit.html.twig';

        return $this->render("server/$template", [
            'server' => $server,
            'form' => $form,
            'title' => 'Editar servidor'
        ]);
    }

    /*#[Route('/{id}', name: 'server_delete', methods: ['POST'])]
    public function delete(Request $request, Server $server, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$server->getId(), $request->request->get('_token'))) {
            $entityManager->remove($server);
            $entityManager->flush();
        }

        return $this->redirectToRoute('server_index', [], Response::HTTP_SEE_OTHER);
    }*/

    #[Route('/state', name: 'server_state', methods: ['POST'])]
    #[IsGranted('ROLE_ADMIN')]
    public function state(Request $request, ServerRepository $serverRepository, CrudActionService $crudActionService): Response
    {
        $template = $crudActionService->stateAction($request, $serverRepository, 'Se ha desactivado el servidor.', 'Se ha activado el servidor.');
        return new Response($template);
    }
}
