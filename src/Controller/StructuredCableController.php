<?php

namespace App\Controller;

use App\Entity\Camera;
use App\Entity\StructuredCable;
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
    #[Route('/new/{camera}', name: 'structured_cable_new', requirements: ['camera' => '\d+'], methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager, Camera $camera): Response
    {
        $structuredCable = new StructuredCable();
        $form = $this->createForm(StructuredCableType::class, $structuredCable, [
            'action' => $this->generateUrl('structured_cable_new', ['camera' => $camera->getId()]),
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try{
                $camera->setStructuredCable($structuredCable);
                $entityManager->persist($structuredCable);
                $entityManager->flush();

                //si el formulario se mando correctamente por ajax
                if($request->isXmlHttpRequest()){
                    return $this->render("partials/_form_success.html.twig", [
                        'id' => 'edit_structured_cable_'.$structuredCable->getId(),
                        'type' => 'text-bg-success',
                        'message' => 'Se ha modificado el cable estructurado.'
                    ]);
                }
            }catch (\Exception $exception){
                if($request->isXmlHttpRequest()){
                    return $this->render("partials/_form_success.html.twig", [
                        'id' => 'edit_structured_cable_'.$structuredCable->getId(),
                        'type' => 'text-bg-danger',
                        'message' => $exception->getMessage()
                    ]);
                }
            }

            $this->addFlash('success', 'Se ha modificado el cable estructurado.');

            return $this->redirectToRoute('camera_index', [], Response::HTTP_SEE_OTHER);
        }

        $template = ($request->isXmlHttpRequest()) ? '_form.html.twig' : 'new.html.twig';

        return $this->render("structured_cable/$template", [
            'structured_cable' => $structuredCable,
            'form' => $form,
            'title' => 'Modificar cable estructurado',
//            'camera' => $camera
        ]);
    }

    #[Route('/{id}/edit/{camera}', name: 'structured_cable_edit', requirements: ['id' => '\d+', 'camera' => '\d+'], methods: ['GET', 'POST'])]
    public function edit(Request $request, StructuredCable $structuredCable, EntityManagerInterface $entityManager, Camera $camera): Response
    {
        $form = $this->createForm(StructuredCableType::class, $structuredCable, [
            'action' => $this->generateUrl('structured_cable_edit', ['id' => $structuredCable->getId(), 'camera' => $camera->getId()]),
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

            return $this->redirectToRoute('camera_index', [], Response::HTTP_SEE_OTHER);
        }

        $template = ($request->isXmlHttpRequest()) ? '_form.html.twig' : 'edit.html.twig';

        return $this->render("structured_cable/$template", [
            'structured_cable' => $structuredCable,
            'form' => $form,
            'title' => 'Modificar cable estructurado',
            'camera' => $camera
        ]);
    }

    #[Route('/{id}/{camera}', name: 'structured_cable_delete', requirements: ['id' => '\d+', 'camera' => '\d+'], methods: ['POST'])]
    public function delete(Request $request, StructuredCable $structuredCable, EntityManagerInterface $entityManager, Camera $camera): Response
    {
        if ($this->isCsrfTokenValid('delete'.$structuredCable->getId(), $request->request->get('_token'))) {
            try{
                $camera->setStructuredCable(null);
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
}
