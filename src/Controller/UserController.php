<?php

namespace App\Controller;

use App\DTO\Paginator;
use App\Form\ProfileFullNameType;
use App\Repository\RoleRepository;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/user')]
class UserController extends AbstractController
{
    #[Route('/', name: 'user_list')]
    #[IsGranted('ROLE_ADMIN')]
    public function index(Request $request, UserRepository $userRepository, RoleRepository $roleRepository): Response
    {
        $filter = $request->query->get('filter', '');
        $amountPerPage = $request->query->get('amount', 10);
        $pageNumber = $request->query->get('page', 1);

        $data = $userRepository->findUsers($filter, $amountPerPage, $pageNumber);

        return $this->render('user/index.html.twig', [
            'filter' => $filter,
            'roles' => $roleRepository->findAll(),
            'paginator' => new Paginator($data, $amountPerPage, $pageNumber)
        ]);
    }

    #[Route('/add_role', name: 'add_role', methods: ['POST'])]
    #[IsGranted('ROLE_ADMIN')]
    public function addRole(Request $request, UserRepository $userRepository, RoleRepository $roleRepository): Response
    {
        if($request->isXmlHttpRequest() && ($request->query->get('fetch') === '1')){
            $user = $userRepository->find($request->request->get('user'));
            $role = $roleRepository->find($request->request->get('role'));

            $user->addRole($role);

            $userRepository->save($user, true);
            return $this->render('user/_add_remove_role.html.twig', [
                'id' => 'add_'.$user->getId().'-'.$role->getId(),
                'role' => $role,
                'user' => $user,
                'action' => 'Establecido',
                'type' => 'text-bg-success',
                'message' => null
            ]);
        }

        throw new BadRequestHttpException('Ajax request');
    }

    #[Route('/remove_role', name: 'remove_role', methods: ['POST'])]
    #[IsGranted('ROLE_ADMIN')]
    public function removeRole(Request $request, UserRepository $userRepository, RoleRepository $roleRepository): Response
    {
        if($request->isXmlHttpRequest() && ($request->query->get('fetch') === '1')){
            $user = $userRepository->find($request->request->get('user'));
            $role = $roleRepository->find($request->request->get('role'));

            try {
                $user->removeRole($role, !$this->isGranted('ROLE_SUPER_ADMIN'));
                $userRepository->save($user, true);
                $data = [
                    'type' => 'text-bg-success',
                    'message' => null
                ];
                $response = new Response();
            }catch (\Exception $exception){
                $data = [
                    'type' => 'text-bg-danger',
                    'message' => $exception->getMessage()
                ];
                $response = new Response('', 422);
            } finally {
                return $this->render('user/_add_remove_role.html.twig', $data + [
                    'id' => 'remove_'.$user->getId().'-'.$role->getId(),
                    'role' => $role,
                    'user' => $user,
                    'action' => 'Eliminado',
                ], $response);
            }
        }

        throw new BadRequestHttpException('Ajax request');
    }

    #[Route('/profile', name: 'user_profile')]
    #[IsGranted('ROLE_USER')]
    public function profile(Request $request, RoleRepository $roleRepository, UserRepository $userRepository): Response
    {
        $form = $this->createForm(ProfileFullNameType::class, $this->getUser());
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            $userRepository->save($this->getUser(), true);

            $this->addFlash('success', 'Datos salvados.');
        }

        return $this->render('user/profile.html.twig', [
            'roles' => $roleRepository->findAll(),
            'form' => $form->createView()
        ]);
    }


}
