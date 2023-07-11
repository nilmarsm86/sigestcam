<?php

namespace App\Controller;

use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/user')]
class UserController extends AbstractController
{
    #[Route('/', name: 'user_list')]
    public function index(Request $request, UserRepository $userRepository): Response
    {
        //manejar el filtro enviado por query string
        $filter = $request->query->get('filter', '');
        //manejar la cantidad a mostrar enviado por query string
        $amountPerPage = $request->query->get('amount', 10);
        //manejar el paginado
        $pageNumber = $request->query->get('page', 1);

        $paginator = $userRepository->findUsers($filter, $amountPerPage, $pageNumber);
        $maxPages = ceil($paginator->count() / $amountPerPage);
        $from = ($pageNumber * $amountPerPage) - $amountPerPage + 1;
        $to = (($pageNumber * $amountPerPage) < $paginator->count()) ? $pageNumber * $amountPerPage : $paginator->count();

        return $this->render('user/index.html.twig', [
            'users' => $paginator,
            'amountPerPage' => $amountPerPage,
            'filter' => $filter,
            'page' => $pageNumber,
            'from' => $from,
            'to' => $to,
            'maxPages' => $maxPages
        ]);
    }

}
