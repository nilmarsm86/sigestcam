<?php

namespace App\Controller;

use App\Controller\Traits\MunicipalityTrait;
use App\Entity\Camera;
use App\Entity\Municipality;
use App\Form\CameraType;
use App\Repository\CameraRepository;
use App\Service\CrudActionService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/camera')]
#[IsGranted('ROLE_BOSS')]
class CameraController extends AbstractController
{
    use MunicipalityTrait;

    #[Route('/', name: 'camera_index', methods: ['GET'])]
    public function index(Request $request, CameraRepository $cameraRepository, CrudActionService $crudActionService): Response
    {
        $template = $crudActionService->indexAction($request, $cameraRepository, 'findCameras', 'camera');
        return new Response($template);
    }

    #[Route('/new', name: 'camera_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $camera = new Camera();
        $form = $this->createForm(CameraType::class, $camera, [
            'action' => $this->generateUrl('camera_new'),
            'crud' => true
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $dataAddress = $request->request->all()['camera']['address'];
            $municipalityId = $dataAddress['municipality'] ?? null;
            $municipality = $this->findMunicipalityForEquipment($entityManager, $municipalityId);

            $camera->setMunicipality($municipality);
            $camera->deactivate();
            $entityManager->persist($camera);
            $entityManager->flush();

            //si el formulario se mando correctamente por ajax
            if($request->isXmlHttpRequest()){
                return $this->render("partials/_form_success.html.twig", [
                    'id' => 'new_camera_'.$camera->getId(),
                    'type' => 'text-bg-success',
                    'message' => 'Se ha agregado una cámara.'
                ]);
            }

            $this->addFlash('success', 'Se ha agregado una cámara.');
            return $this->redirectToRoute('camera_index', [], Response::HTTP_SEE_OTHER);
        }

        $template = ($request->isXmlHttpRequest()) ? '_form.html.twig' : 'new.html.twig';

        return $this->render("camera/$template", [
            'camera' => $camera,
            'form' => $form,
            'title' => 'Nueva cámara'
        ]);
    }

    #[Route('/{id}', name: 'camera_show', requirements: ['id' => '\d+'], methods: ['GET'])]
    public function show(Request $request, Camera $camera, CrudActionService $crudActionService): Response
    {
        $template = $crudActionService->showAction($request, $camera, 'camera', 'camera', 'Detalles de la cámara');
        return new Response($template);
    }

    #[Route('/{id}/edit', name: 'camera_edit', requirements: ['id' => '\d+'], methods: ['GET', 'POST'])]
    public function edit(Request $request, Camera $camera, EntityManagerInterface $entityManager): Response
    {
        $postData = $request->request->all();

        $provinceId = isset($postData['camera'])
            ? $request->request->all()['camera']['address']['province']
            : $camera->getMunicipality()->getProvince()->getId();

        $municipalityId = $this->findMunicipalityForExistEquipment($camera, $request, 'camera');

        $form = $this->createForm(CameraType::class, $camera, [
            'action' => $this->generateUrl('camera_edit', ['id' => $camera->getId()]),
            'province' => (int) $provinceId,
            'municipality' => (int) $municipalityId,
            'crud' => true
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $municipality = $this->findMunicipalityForEquipment($entityManager, $municipalityId);
            $camera->setMunicipality($municipality);
            $entityManager->flush();

            //si el formulario se mando correctamente por ajax
            if($request->isXmlHttpRequest()){
                return $this->render("partials/_form_success.html.twig", [
                    'id' => 'edit_camera_'.$camera->getId(),
                    'type' => 'text-bg-success',
                    'message' => 'Se ha modificado la cámara.'
                ]);
            }

            $this->addFlash('success', 'Se ha modificado la cámara.');

            return $this->redirectToRoute('camera_index', [], Response::HTTP_SEE_OTHER);
        }

        $template = ($request->isXmlHttpRequest()) ? '_form.html.twig' : 'edit.html.twig';

        return $this->render("camera/$template", [
            'camera' => $camera,
            'form' => $form,
            'title' => 'Editar cámara'
        ]);
    }

    /*#[Route('/{id}', name: 'app_camera_delete', methods: ['POST'])]
    public function delete(Request $request, Camera $camera, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$camera->getId(), $request->request->get('_token'))) {
            $entityManager->remove($camera);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_camera_index', [], Response::HTTP_SEE_OTHER);
    }*/

    #[Route('/state', name: 'camera_state', methods: ['POST'])]
    public function state(Request $request, CameraRepository $cameraRepository, CrudActionService $crudActionService): Response
    {
        $template = $crudActionService->stateAction($request, $cameraRepository, 'Se ha desactivado la cámara.', 'Se ha activado la cámara.');
        return new Response($template);
    }

}
