<?php

namespace App\Controller;

use App\Repository\RoleRepository;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/user')]
#[IsGranted('ROLE_ADMIN')]
class UserController extends AbstractController
{
    #[Route('/', name: 'user_list')]
    public function index(Request $request, UserRepository $userRepository, RoleRepository $roleRepository): Response
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

        $roles = $roleRepository->findAll();

        return $this->render('user/index.html.twig', [
            'users' => $paginator,
            'amountPerPage' => $amountPerPage,
            'filter' => $filter,
            'page' => $pageNumber,
            'from' => $from,
            'to' => $to,
            'maxPages' => $maxPages,
            'roles' => $roles
        ]);
    }

    #[Route('/add_role', name: 'add_role', methods: ['POST'])]
    public function addRole(Request $request, UserRepository $userRepository, RoleRepository $roleRepository): Response
    {
        if($request->isXmlHttpRequest()){
            $user = $userRepository->find($request->request->get('user'));
            $role = $roleRepository->find($request->request->get('role'));

            $user->addRole($role);

            $userRepository->save($user, true);
            return $this->json(['status' => 'OK']);
        }

        throw new BadRequestHttpException('Ajax request');
    }


}
