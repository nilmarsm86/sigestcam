<?php

namespace App\Controller;

use App\Entity\Card;
use App\Entity\Commutator;
use App\Entity\Enums\PortType;
use App\Entity\Msam;
use App\Form\CardType;
use App\Repository\CardRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/card')]
class CardController extends AbstractController
{
    #[Route('/msam/{msam}', name: 'card_index', requirements: ['msam' => '\d+'], methods: ['GET'])]
    public function index(Request $request, CardRepository $cardRepository, Msam $msam): Response
    {
        $template = ($request->isXmlHttpRequest()) ? '_manage.html.twig' : 'index.html.twig';

        return $this->render("card/$template", [
            'cards' => $cardRepository->findBy(['msam' => $msam]),
            'msam' => $msam,
            'paginator' => null
        ]);
    }

    #[Route('/new/msam/{msam}', name: 'card_new', requirements: ['msam' => '\d+'], methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager, Msam $msam): Response
    {
        $card = new Card();
        $form = $this->createForm(CardType::class, $card, [
            'action' => $this->generateUrl('card_new', ['msam' => $msam->getId()]),
            'msam' => $msam->getId()
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $card->setMsam($msam);
            $entityManager->persist($card);
            $entityManager->flush();

            //si el formulario se mando correctamente por ajax
            if($request->isXmlHttpRequest()){
                return $this->render("partials/_form_success.html.twig", [
                    'id' => 'new_card_'.$card->getId(),
                    'type' => 'text-bg-success',
                    'message' => 'Se ha agregado una targeta al msam.'
                ]);
            }

            $this->addFlash('success', 'Se ha agregado una targeta al msam.');
            return $this->redirectToRoute('card_index', ['msam' => $msam->getId()], Response::HTTP_SEE_OTHER);
        }

        $template = ($request->isXmlHttpRequest()) ? '_form.html.twig' : 'new.html.twig';

        return $this->render("card/$template", [
            'card' => $card,
            'form' => $form,
            'msam' => $msam,
            'title' => 'Nueva targeta'
        ]);
    }

    /*#[Route('/{id}/msam/{msam}', name: 'card_show', methods: ['GET'])]
    public function show(Card $card, Msam $msam): Response
    {
        return $this->render('card/show.html.twig', [
            'card' => $card,
            'msam' => $msam,
        ]);
    }*/

    #[Route('/{id}/edit/msam/{msam}', name: 'card_edit', requirements: ['id' => '\d+', 'msam' => '\d+'], methods: ['GET', 'POST'])]
    public function edit(Request $request, Card $card, EntityManagerInterface $entityManager, Msam $msam): Response
    {
        $form = $this->createForm(CardType::class, $card, [
            'action' => $this->generateUrl('card_edit', ['id' => $card->getId(), 'msam' => $msam->getId()]),
            'msam' => $msam->getId()
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            //si el formulario se mando correctamente por ajax
            if($request->isXmlHttpRequest()){
                return $this->render("partials/_form_success.html.twig", [
                    'id' => 'edit_card_'.$card->getId(),
                    'type' => 'text-bg-success',
                    'message' => 'Se ha modificado la targeta del msam.'
                ]);
            }

            $this->addFlash('success', 'Se ha modificado la targeta del msam.');
            return $this->redirectToRoute('card_index', ['msam' => $msam->getId()], Response::HTTP_SEE_OTHER);
        }

        $template = ($request->isXmlHttpRequest()) ? '_form.html.twig' : 'edit.html.twig';

        return $this->render("card/$template", [
            'card' => $card,
            'form' => $form,
            'msam' => $msam,
            'title' => 'Editar targeta'
        ]);
    }

    /*#[Route('/{id}', name: 'card_delete', methods: ['POST'])]
    public function delete(Request $request, Card $card, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$card->getId(), $request->request->get('_token'))) {
            $entityManager->remove($card);
            $entityManager->flush();
        }

        return $this->redirectToRoute('card_index', [], Response::HTTP_SEE_OTHER);
    }*/

    #[Route('/port/{id}', name: 'card_port', requirements: ['id' => '\d+'], methods: ['GET'])]
    public function port(Request $request, Card $card): Response
    {
        $template = ($request->isXmlHttpRequest()) ? '_port_detail.html.twig' : 'port.html.twig';

        return $this->render("card/$template", [
            'card' => $card,
            'title' => 'Puertos de la targeta',
            'forSelect' => PortType::forSelect(),
        ]);
    }
}
