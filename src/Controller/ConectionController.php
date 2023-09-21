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
            'direct_percent' => ($totalAmountConnections > 0) ? $directConnections * 100 / $totalAmountConnections : 0,
            'simple_connections' => $simpleConnections,
            'simple_percent' => ($totalAmountConnections > 0) ? $simpleConnections * 100 / $totalAmountConnections : 0,
            'slave_switch_connections' => $slaveSwitchConnections,
            'slave_switch_percent' => ($totalAmountConnections > 0) ? $slaveSwitchConnections * 100 / $totalAmountConnections : 0,
            'slave_modem_connections' => $slaveModemConnections,
            'slave_modem_percent' => ($totalAmountConnections > 0) ? $slaveModemConnections * 100 / $totalAmountConnections : 0,
            'full_connections' => $fullConnections,
            'full_percent' => ($totalAmountConnections > 0) ? $fullConnections * 100 / $totalAmountConnections : 0,
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

    #[Route('/simple_new', name: 'simple_new')]
    public function simpleNew(): Response
    {
        return $this->render('connection/simple_new.html.twig');
    }

    #[Route('/simple_list', name: 'simple_list')]
    public function simpleList(Request $request): Response
    {
        return $this->render('connection/simple_list.html.twig', [
            'filter' => $request->query->get('filter', '')
        ]);
    }

    #[Route('/slave_switch_new', name: 'slave_switch_new')]
    public function slaveSwitchNew(): Response
    {
        return $this->render('connection/slave_switch_new.html.twig');
    }

    #[Route('/slave_switch_list', name: 'slave_switch_list')]
    public function slaveSwitchList(Request $request): Response
    {
        return $this->render('connection/slave_switch_list.html.twig', [
            'filter' => $request->query->get('filter', '')
        ]);
    }

    #[Route('/slave_modem_new', name: 'slave_modem_new')]
    public function slaveModemNew(): Response
    {
        return $this->render('connection/slave_modem_new.html.twig');
    }

    #[Route('/slave_modem_list', name: 'slave_modem_list')]
    public function slaveModemList(Request $request): Response
    {
        return $this->render('connection/slave_modem_list.html.twig', [
            'filter' => $request->query->get('filter', '')
        ]);
    }

}
