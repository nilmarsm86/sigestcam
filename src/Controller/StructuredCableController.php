<?php

namespace App\Controller;

use App\Entity\Camera;
use App\Entity\Modem;
use App\Entity\StructuredCable;
use App\Entity\Traits\ConnectedTrait;
use App\Form\StructuredCableType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/structured/cable')]
class StructuredCableController extends AbstractController
{
    #[Route('/new_in_camera/{camera}', name: 'structured_cable_new_camera', requirements: ['camera' => '\d+'], methods: ['GET', 'POST'])]
    public function newCableInCamera(Request $request, EntityManagerInterface $entityManager, Camera $camera): Response
    {
        $template = ($request->isXmlHttpRequest()) ? '_form_camera.html.twig' : 'new.html.twig';
        return $this->newCable(
            $request,
            $entityManager,
            $camera,
            'structured_cable_new_camera',
            ['camera' => $camera->getId()],
            'camera_index',
            $template
        );
    }

    #[Route('edit_in_camera/{id}/edit/{camera}', name: 'structured_cable_edit_camera', requirements: ['id' => '\d+', 'camera' => '\d+'], methods: ['GET', 'POST'])]
    public function editCableInCamera(Request $request, StructuredCable $structuredCable, EntityManagerInterface $entityManager, Camera $camera): Response
    {
        $template = ($request->isXmlHttpRequest()) ? '_form_camera.html.twig' : 'edit.html.twig';
        return $this->editCable($request, $structuredCable, $entityManager, $camera, 'structured_cable_edit_camera', ['id' => $structuredCable->getId(), 'camera' => $camera->getId()], 'camera_index', $template);
    }

    #[Route('delete_in_camera/{id}/{camera}', name: 'structured_cable_delete_camera', requirements: ['id' => '\d+', 'camera' => '\d+'], methods: ['POST'])]
    public function deleteCableInCamera(Request $request, StructuredCable $structuredCable, EntityManagerInterface $entityManager, Camera $camera): Response
    {
        return $this->deleteCable($request, $structuredCable, $entityManager, $camera);
    }

    #[Route('/new_in_modem/{modem}', name: 'structured_cable_new_modem', requirements: ['modem' => '\d+'], methods: ['GET', 'POST'])]
    public function newCableInModem(Request $request, EntityManagerInterface $entityManager, Modem $modem): Response
    {
        $template = ($request->isXmlHttpRequest()) ? '_form_modem.html.twig' : 'new.html.twig';
        return $this->newCable(
            $request,
            $entityManager,
            $modem,
            'structured_cable_new_modem',
            ['modem' => $modem->getId()],
            'modem_index',
            $template
        );
    }

    #[Route('edit_in_modem/{id}/edit/{modem}', name: 'structured_cable_edit_modem', requirements: ['id' => '\d+', 'modem' => '\d+'], methods: ['GET', 'POST'])]
    public function editCableInModem(Request $request, StructuredCable $structuredCable, EntityManagerInterface $entityManager, Modem $modem): Response
    {
        $template = ($request->isXmlHttpRequest()) ? '_form_modem.html.twig' : 'edit.html.twig';
        return $this->editCable($request, $structuredCable, $entityManager, $modem, 'structured_cable_edit_modem', ['id' => $structuredCable->getId(), 'modem' => $modem->getId()], 'camera_index', $template);
    }

    #[Route('delete_in_modem/{id}/{camera}', name: 'structured_cable_delete_modem', requirements: ['id' => '\d+', 'camera' => '\d+'], methods: ['POST'])]
    public function deleteCableInModem(Request $request, StructuredCable $structuredCable, EntityManagerInterface $entityManager, Modem $modem): Response
    {
        return $this->deleteCable($request, $structuredCable, $entityManager, $modem);
    }

    private function deleteCable(Request $request, StructuredCable $structuredCable, EntityManagerInterface $entityManager, $entity): Response
    {
        if ($this->isCsrfTokenValid('delete'.$structuredCable->getId(), $request->request->get('_token'))) {
            try{
                $entity->setStructuredCable(null);
                $entityManager->remove($structuredCable);
                $entityManager->flush();

                return $this->render("partials/_form_success.html.twig", [
                    'id' => 'delete_structured_cable',
                    'type' => 'text-bg-success',
                    'message' => 'Se ha eliminado el cable estructurado.'
                ]);
            }catch (\Exception $exception){
                return $this->render("partials/_form_success.html.twig", [
                    'id' => 'delete_structured_cable',
                    'type' => 'text-bg-danger',
                    'message' => $exception->getMessage()
                ]);
            }
        }

        throw new BadRequestHttpException('Ajax request');
    }

    private function newCable(Request $request, EntityManagerInterface $entityManager, $entity, $formPath, $formPathParams, $pathIndex, $template): Response
    {
        $structuredCable = new StructuredCable();
        $form = $this->createForm(StructuredCableType::class, $structuredCable, [
            'action' => $this->generateUrl($formPath, $formPathParams),
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try{
                $entity->setStructuredCable($structuredCable);
                $entityManager->persist($structuredCable);
                $entityManager->flush();

                //si el formulario se mando correctamente por ajax
                if($request->isXmlHttpRequest()){
                    return $this->render("partials/_form_success.html.twig", [
                        'id' => 'new_structured_cable_'.$structuredCable->getId(),
                        'type' => 'text-bg-success',
                        'message' => 'Se ha modificado el cable estructurado.'
                    ]);
                }
            }catch (\Exception $exception){
                if($request->isXmlHttpRequest()){
                    return $this->render("partials/_form_success.html.twig", [
                        'id' => 'new_structured_cable_'.$structuredCable->getId(),
                        'type' => 'text-bg-danger',
                        'message' => $exception->getMessage()
                    ]);
                }
            }

            $this->addFlash('success', 'Se ha modificado el cable estructurado.');
            return $this->redirectToRoute($pathIndex, [], Response::HTTP_SEE_OTHER);
        }

//        $template = ($request->isXmlHttpRequest()) ? '_form_modem.html.twig' : 'new.html.twig';

        return $this->render("structured_cable/$template", [
            'structured_cable' => $structuredCable,
            'form' => $form,
            'title' => 'Modificar cable estructurado',
        ]);
    }

    public function editCable(Request $request, StructuredCable $structuredCable, EntityManagerInterface $entityManager, $entity, $formPath, $formPathParams, $pathIndex, $template): Response
    {
        $form = $this->createForm(StructuredCableType::class, $structuredCable, [
            'action' => $this->generateUrl($formPath, $formPathParams),
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            //si el formulario se mando correctamente por ajax
            if($request->isXmlHttpRequest()){
                return $this->render("partials/_form_success.html.twig", [
                    'id' => 'edit_structured_cable_'.$structuredCable->getId(),
                    'type' => 'text-bg-success',
                    'message' => 'Se ha modificado el cable estructurado.'
                ]);
            }

            $this->addFlash('success', 'Se ha modificado el cable estructurado.');

            return $this->redirectToRoute($pathIndex, [], Response::HTTP_SEE_OTHER);
        }

        return $this->render("structured_cable/$template", [
            'structured_cable' => $structuredCable,
            'form' => $form,
            'title' => 'Modificar cable estructurado',
            'entity' => $entity
        ]);
    }

}
