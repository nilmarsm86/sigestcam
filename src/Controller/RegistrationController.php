<?php

namespace App\Controller;

use App\DTO\RegistrationForm;
use App\Form\RegistrationFormType;
use App\Repository\RoleRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;

class RegistrationController extends AbstractController
{
    #[Route('/register', name: 'app_register')]
    public function register(Request $request, UserPasswordHasherInterface $userPasswordHasher, UserRepository $userRepository, RoleRepository $rolRepository): Response
    {
        if(!is_null($this->getUser())){
            return $this->redirectToRoute('app_home');
        }

        $form = $this->createForm(RegistrationFormType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $roleUser = $rolRepository->findOneBy(['name' => 'ROLE_USER']);

            /** @var RegistrationForm $dto */
            $dto = $form->getData();
            $user = $dto->toEntity();
            $user->register($userPasswordHasher, $roleUser);

            $userRepository->save($user, true);

            // do anything else you need here, like send an email
            $this->addFlash('success', 'Se a registrado correctamente en el sistema. Espere que le activen el usuario para autenticarse.');

            return $this->redirectToRoute('app_login');
        }

        return $this->render('registration/register.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
