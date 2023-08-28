<?php

namespace App\Controller;

use App\DTO\Paginator;
use App\Entity\Enums\ConnectionType;
use App\Repository\CommutatorRepository;
use App\Repository\PortRepository;
use Doctrine\ORM\AbstractQuery;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;

#[Route('/connection', name: 'connection_')]
class ConectionController extends AbstractController
{
    #[Route('/', name: 'index')]
    public function index(PortRepository $portRepository): Response
    {
        $totalAmountConnections = $portRepository->findAmountConnections();
        $directConnections = $portRepository->findAmountDirectConnections();
        $simpleConnections = $portRepository->findAmountSimpleConnections();
        $slaveSwitchConnections = $portRepository->findAmountSlaveSwitchConnections();
        $slaveModemConnections = $portRepository->findAmountSlaveModemConnections();
        $fullConnections = $portRepository->findAmountFullConnections();

        return $this->render('connection/index.html.twig', [
            'total_amount_connections' => $totalAmountConnections,
            'direct_connections' => $directConnections,
            'simple_connections' => $simpleConnections,
            'slave_switch_connections' => $slaveSwitchConnections,
            'slave_modem_connections' => $slaveModemConnections,
            'full_connections' => $fullConnections,
        ]);
    }

    #[Route('/direct_new', name: 'direct_new')]
    public function directNew(
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
//        $filter = $request->query->get('filter', '');
//        $amountPerPage = $request->query->getInt('amount', 10);
//        $pageNumber = $request->query->getInt('page', 1);
//
//        $data = $commutatorRepository->findCommutator($filter, $amountPerPage, $pageNumber);

        return $this->render('connection/direct_new.html.twig', [
            //'commutator' => $commutatorFormModel,
//            'filter' => $filter,
//            'paginator' => new Paginator($data, $amountPerPage, $pageNumber),
//            'data' => $data->getQuery()->getResult(AbstractQuery::HYDRATE_ARRAY),
//            'amountPerPage' => $amountPerPage,
//            'pageNumber' => $pageNumber,
//            'fake' => $data->count(),
//            'connection' => ConnectionType::Direct
        ]);
    }

    #[Route('/direct_list', name: 'direct_list')]
    public function directList(Request $request): Response
    {
        return $this->render('connection/direct_list.html.twig', [
            'filter' => $request->query->get('filter', '')
        ]);
    }

}
