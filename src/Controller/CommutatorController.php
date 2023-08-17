<?php

namespace App\Controller;

use App\DTO\Paginator;
use App\Entity\Commutator;
use App\Entity\Municipality;
use App\Entity\Province;
use App\Form\Commutator1Type;
use App\Form\CommutatorType;
use App\Repository\CommutatorRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/commutator')]
class CommutatorController extends AbstractController
{
    #[Route('/', name: 'commutator_index', methods: ['GET'])]
    public function index(Request $request, CommutatorRepository $commutatorRepository): Response
    {
        $filter = $request->query->get('filter', '');
        $amountPerPage = $request->query->get('amount', 10);
        $pageNumber = $request->query->get('page', 1);

        $data = $commutatorRepository->findCommutator($filter, $amountPerPage, $pageNumber);

        $template = ($request->isXmlHttpRequest()) ? '_list.html.twig' : 'index.html.twig';

        return $this->render("commutator/$template", [
            'filter' => $filter,
            'paginator' => new Paginator($data, $amountPerPage, $pageNumber)
        ]);
    }

    #[Route('/new', name: 'app_crud_commutator_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $commutator = new Commutator();
        $form = $this->createForm(CommutatorType::class, $commutator, [
            'action' => $this->generateUrl('app_crud_commutator_new')
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $municipalityId = $request->request->all()['commutator']['address']['municipality'];
            $municipality = $entityManager->getRepository(Municipality::class)->find($municipalityId);
            $commutator->setMunicipality($municipality);
            //dump($commutator);
            $entityManager->persist($commutator);
            $entityManager->flush();

            //si el formulario se mando correctamente por ajax
            if($request->isXmlHttpRequest()){
                return new Response(null, 204);
            }

            return $this->redirectToRoute('commutator_index', [], Response::HTTP_SEE_OTHER);
        }

        $template = ($request->isXmlHttpRequest()) ? '_form.html.twig' : 'new.html.twig';

        return $this->render("commutator/$template", [
            'commutator' => $commutator,
            'form' => $form,
            'title' => 'Nuevo switch'
        ]);
    }

    #[Route('/{id}', name: 'app_crud_commutator_show', requirements: ['id' => '\d+'], methods: ['GET'])]
    public function show(Request $request, Commutator $commutator): Response
    {
        $template = ($request->isXmlHttpRequest()) ? '_detail.html.twig' : 'show.html.twig';

        return $this->render("commutator/$template", [
            'commutator' => $commutator,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_crud_commutator_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Commutator $commutator, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(CommutatorType::class, $commutator, [
            'action' => $this->generateUrl('app_crud_commutator_edit', ['id' => $commutator->getId()]),
            'province' => $commutator->getMunicipality()->getProvince()->getId(),
            'municipality' => $commutator->getMunicipality()->getId()
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            //si el formulario se mando correctamente por ajax
            if($request->isXmlHttpRequest()){
                return new Response(null, 204);
            }

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

    #[Route('/municipality/{id}', name: 'commutator_municipality', requirements: ['id' => '\d+'], methods: ['GET'])]
    public function municipality(Province $province): Response
    {
        $response = [];
        foreach ($province->getMunicipalities() as $municipality){
            $response[] = '<option value="'.$municipality->getId().'">'.$municipality->getName().'</option>';
        }

        return new Response(join('', $response));
    }
}
