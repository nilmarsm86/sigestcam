<?php

namespace App\Controller;

use App\Entity\Commutator;
use App\Form\Commutator1Type;
use App\Repository\CommutatorRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/crud/commutator')]
class CrudCommutatorController extends AbstractController
{
    #[Route('/', name: 'app_crud_commutator_index', methods: ['GET'])]
    public function index(CommutatorRepository $commutatorRepository): Response
    {
        return $this->render('crud_commutator/index.html.twig', [
            'commutators' => $commutatorRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_crud_commutator_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $commutator = new Commutator();
        $form = $this->createForm(Commutator1Type::class, $commutator);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($commutator);
            $entityManager->flush();

            return $this->redirectToRoute('app_crud_commutator_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('crud_commutator/new.html.twig', [
            'commutator' => $commutator,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_crud_commutator_show', methods: ['GET'])]
    public function show(Commutator $commutator): Response
    {
        return $this->render('crud_commutator/show.html.twig', [
            'commutator' => $commutator,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_crud_commutator_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Commutator $commutator, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(Commutator1Type::class, $commutator);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_crud_commutator_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('crud_commutator/edit.html.twig', [
            'commutator' => $commutator,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_crud_commutator_delete', methods: ['POST'])]
    public function delete(Request $request, Commutator $commutator, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$commutator->getId(), $request->request->get('_token'))) {
            $entityManager->remove($commutator);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_crud_commutator_index', [], Response::HTTP_SEE_OTHER);
    }
}
