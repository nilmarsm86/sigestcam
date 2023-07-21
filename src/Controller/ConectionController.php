<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ConectionController extends AbstractController
{
    #[Route('/conection', name: 'app_conection')]
    public function index(): Response
    {
        return $this->render('conection/index.html.twig', [
            'controller_name' => 'ConectionController',
        ]);
    }
}
