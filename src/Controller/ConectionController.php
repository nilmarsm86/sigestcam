<?php

namespace App\Controller;

use App\DTO\CommutatorForm;
use App\DTO\Paginator;
use App\Entity\Commutator;
use App\Entity\Municipality;
use App\Entity\Province;
use App\Form\CommutatorType;
use App\Repository\CommutatorRepository;
use App\Resolver\RequestFormPayloadValueResolver;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/conection', name: 'conection')]
class ConectionController extends AbstractController
{
    #[Route('/', name: '_index')]
    public function index(): Response
    {
        return $this->render('conection/index.html.twig', [
            'controller_name' => 'ConectionController',
        ]);
    }

    #[Route('/direct', name: '_direct')]
    public function direct(
        Request $request,
        /*#[MapRequestPayload(
            serializationContext: [
                'entities'=>[Province::class, Municipality::class],
                'form' => 'commutator'
            ],
            resolver: RequestFormPayloadValueResolver::class,
        )]
        ?CommutatorForm $commutatorForm = null*/
        CommutatorRepository $commutatorRepository
    ): Response
    {
        /*$form = $this->createForm(CommutatorType::class, $commutatorForm);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){
            dump($commutatorForm);
            //die();
        }*/

        $filter = $request->query->get('filter', '');
        $amountPerPage = $request->query->getInt('amount', 10);
        $pageNumber = $request->query->getInt('page', 1);

        $data = $commutatorRepository->findCommutator($filter, $amountPerPage, $pageNumber);

        return $this->render('conection/direct.html.twig', [
            'controller_name' => 'ConectionController',
            //'form' => $form,
            //'commutator' => $form->getData(),
            'filter' => $filter,
            'paginator' => new Paginator($data, $amountPerPage, $pageNumber)
        ]);
    }

}
