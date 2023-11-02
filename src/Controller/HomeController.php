<?php

namespace App\Controller;

use App\DTO\Paginator;
use App\Entity\Camera;
use App\Entity\Commutator;
use App\Entity\Modem;
use App\Entity\Msam;
use App\Entity\Port;
use App\Entity\Report;
use App\Entity\Server;
use App\Repository\EquipmentRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    #[Route('/', name: 'app_home')]
    public function index(EntityManagerInterface $entityManager): Response
    {
        $totalCameras = $entityManager->getRepository(Camera::class)->findAmountCameras();
        $totalModems = $entityManager->getRepository(Modem::class)->findAmountModem();
        $totalServer = $entityManager->getRepository(Server::class)->findAmountServers();
        $totalMsam = $entityManager->getRepository(Msam::class)->findAmountMsam();
        $totalCommutator = $entityManager->getRepository(Commutator::class)->findAmountCommutators();

        $conections[] = (string) $entityManager->getRepository(Port::class)->findAmountDirectConnections();
        $conections[] = (string) $entityManager->getRepository(Port::class)->findAmountSimpleConnections();
        $conections[] = (string) $entityManager->getRepository(Port::class)->findAmountSlaveSwitchConnections();
        $conections[] = (string) $entityManager->getRepository(Port::class)->findAmountSlaveModemConnections();
        $conections[] = (string) $entityManager->getRepository(Port::class)->findAmountFullConnections();

        $reports = [];
        $months = ["01","02","03","04","05","06","07","08","09","10","11","12",];
        foreach ($months as $month){
            $reports[] = (string) $entityManager->getRepository(Report::class)->findOpenAmountReportsByMonth($month);
        }

        return is_null($this->getUser()) ? $this->redirectToRoute('app_login') : $this->render('home/index.html.twig', [
            'controller_name' => 'HomeController',
            'totalCameras' => $totalCameras,
            'totalModems' => $totalModems,
            'totalServer' => $totalServer,
            'totalMsam' => $totalMsam,
            'totalCommutator' => $totalCommutator,
            'conections' => $conections,
            'reports' => $reports
        ]);
    }

    #[Route('/search', name: 'app_search')]
    public function search(Request $request, EquipmentRepository $equipmentRepository): Response
    {
        $ip = $request->query->get('ip', '');

        if(empty($ip)){
            return $this->redirectToRoute('app_home');
        }

        $amountPerPage = $request->query->get('amount', 10);
        $pageNumber = $request->query->get('page', 1);
        $data = $equipmentRepository->generalSearch($ip, $amountPerPage, $pageNumber);

        return $this->render('home/search.html.twig', [
            'ip' => $ip,
            'filter' => $ip,
            'paginator' => new Paginator($data, $amountPerPage, $pageNumber)
        ]);
    }


}
