<?php

namespace App\Controller;

use App\Entity\Commutator;
use App\Entity\Enums\PortType;
use App\Entity\Municipality;
use App\Form\CommutatorType;
use App\Repository\CommutatorRepository;
use App\Service\CrudActionService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/commutator')]
class CommutatorController extends AbstractController
{
    #[Route('/', name: 'commutator_index', methods: ['GET'])]
    public function index(Request $request, CommutatorRepository $commutatorRepository, CrudActionService $crudActionService): Response
    {
        $template = $crudActionService->indexAction($request, $commutatorRepository, 'findCommutator', 'commutator');
        return new Response($template);
    }

    #[Route('/new', name: 'commutator_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $commutator = new Commutator();
        $form = $this->createForm(CommutatorType::class, $commutator, [
            'action' => $this->generateUrl('commutator_new')
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $municipalityId = $request->request->all()['commutator']['address']['municipality'];
            $municipality = $entityManager->getRepository(Municipality::class)->find($municipalityId);
            $commutator->setMunicipality($municipality);
            $commutator->deactivate();
            $entityManager->persist($commutator);
            $entityManager->flush();

            //si el formulario se mando correctamente por ajax
            if($request->isXmlHttpRequest()){
                return $this->render("partials/_form_success.html.twig", [
                    'id' => 'new_commutator_'.$commutator->getId(),
                    'type' => 'text-bg-success',
                    'message' => 'Se ha agregado un switch.'
                ]);
            }

            $this->addFlash('success', 'Se ha agregado un switch.');
            return $this->redirectToRoute('commutator_index', [], Response::HTTP_SEE_OTHER);
        }

        $template = ($request->isXmlHttpRequest()) ? '_form.html.twig' : 'new.html.twig';

        return $this->render("commutator/$template", [
            'commutator' => $commutator,
            'form' => $form,
            'title' => 'Nuevo switch'
        ]);
    }

    #[Route('/{id}', name: 'commutator_show', requirements: ['id' => '\d+'], methods: ['GET'])]
    public function show(Request $request, Commutator $commutator, CrudActionService $crudActionService): Response
    {
        $template = $crudActionService->showAction($request, $commutator, 'commutator', 'commutator', 'Detalles del switch');
        return new Response($template);
    }

    #[Route('/{id}/edit', name: 'commutator_edit', requirements: ['id' => '\d+'], methods: ['GET', 'POST'])]
    public function edit(Request $request, Commutator $commutator, EntityManagerInterface $entityManager): Response
    {
        $postData = $request->request->all();

        $provinceId = isset($postData['commutator'])
            ? $request->request->all()['commutator']['address']['province']
            : $commutator->getMunicipality()->getProvince()->getId();

        $municipalityId = isset($postData['commutator'])
            ? $request->request->all()['commutator']['address']['municipality']
            : $commutator->getMunicipality()->getId();

        $form = $this->createForm(CommutatorType::class, $commutator, [
            'action' => $this->generateUrl('commutator_edit', ['id' => $commutator->getId()]),
            'province' => (int) $provinceId,
            'municipality' => (int) $municipalityId
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $municipality = $entityManager->getRepository(Municipality::class)->find($municipalityId);
            $commutator->setMunicipality($municipality);
            $entityManager->flush();

            //si el formulario se mando correctamente por ajax
            if($request->isXmlHttpRequest()){
                return $this->render("partials/_form_success.html.twig", [
                    'id' => 'edit_commutator_'.$commutator->getId(),
                    'type' => 'text-bg-success',
                    'message' => 'Se ha modificado el switch'
                ]);
            }

            $this->addFlash('success', 'Se ha modificado el switch');

            return $this->redirectToRoute('commutator_index', [], Response::HTTP_SEE_OTHER);
        }

        $template = ($request->isXmlHttpRequest()) ? '_form.html.twig' : 'edit.html.twig';

        return $this->render("commutator/$template", [
            'commutator' => $commutator,
            'form' => $form,
            'title' => 'Editar switch'
        ]);
    }

//    #[Route('/{id}', name: 'app_crud_commutator_delete', methods: ['POST'])]
//    public function delete(Request $request, Commutator $commutator, EntityManagerInterface $entityManager): Response
//    {
//        if ($this->isCsrfTokenValid('delete'.$commutator->getId(), $request->request->get('_token'))) {
//            $entityManager->remove($commutator);
//            $entityManager->flush();
//        }
//
//        return $this->redirectToRoute('commutator_index', [], Response::HTTP_SEE_OTHER);
//    }

    #[Route('/state', name: 'commutator_state', methods: ['POST'])]
    public function state(Request $request, CommutatorRepository $commutatorRepository, CrudActionService $crudActionService): Response
    {
        $template = $crudActionService->stateAction($request, $commutatorRepository, 'Se ha desactivado el switch', 'Se ha activado el switch');
        return new Response($template);
    }

    #[Route('/port/{id}', name: 'commutator_port', requirements: ['id' => '\d+'], methods: ['GET'])]
    public function port(Request $request, Commutator $commutator): Response
    {
        $template = ($request->isXmlHttpRequest()) ? '_port_detail.html.twig' : 'port.html.twig';

        return $this->render("commutator/$template", [
            'commutator' => $commutator,
            'title' => 'Puertos del switch',
            'forSelect' => PortType::forSelect(),
        ]);
    }


}
