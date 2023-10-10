<?php

namespace App\Controller;

use App\DTO\Paginator;
use App\Repository\CameraRepository;
use App\Repository\EquipmentRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    #[Route('/', name: 'app_home')]
    public function index(): Response
    {
        return is_null($this->getUser()) ? $this->redirectToRoute('app_login') : $this->render('home/index.html.twig', [
            'controller_name' => 'HomeController',
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
