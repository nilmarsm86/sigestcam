<?php

namespace App\Controller;

use App\DTO\Paginator;
use App\Entity\Enums\ConnectionType;
use App\Repository\CommutatorRepository;
use Doctrine\ORM\AbstractQuery;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;

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
        Request              $request,
        /*#[MapRequestPayload(
            serializationContext: [
                'entities'=>[Province::class, Municipality::class],
                'form' => 'commutator'
            ],
            resolver: RequestFormPayloadValueResolver::class,
        )]
        ?CommutatorFormModel $commutatorFormModel = null,*/
        CommutatorRepository $commutatorRepository
    ): Response
    {
        $filter = $request->query->get('filter', '');
        $amountPerPage = $request->query->getInt('amount', 10);
        $pageNumber = $request->query->getInt('page', 1);

        $data = $commutatorRepository->findCommutator($filter, $amountPerPage, $pageNumber);

        return $this->render('conection/direct.html.twig', [
            //'commutator' => $commutatorFormModel,
            'filter' => $filter,
            'paginator' => new Paginator($data, $amountPerPage, $pageNumber),
            'data' => $data->getQuery()->getResult(AbstractQuery::HYDRATE_ARRAY),
            'amountPerPage' => $amountPerPage,
            'pageNumber' => $pageNumber,
            'fake' => $data->count(),
            'connection' => ConnectionType::Direct
        ]);
    }

}
