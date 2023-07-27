<?php

namespace App\Controller;

use App\DTO\CommutatorForm;
use App\Form\CommutatorType;
use App\Repository\MunicipalityRepository;
use App\Repository\ProvinceRepository;
use App\Resolver\RequestFormPayloadValueResolver;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/conection', name: 'conection')]
class ConectionController extends AbstractController
{
    #[Route('/')]
    public function index(): Response
    {
        return $this->render('conection/index.html.twig', [
            'controller_name' => 'ConectionController',
        ]);
    }

    #[Route('/direct', name: '_direct')]
    public function direct(
        Request $request,
        ProvinceRepository $provinceRepository,
        MunicipalityRepository $municipalityRepository,
        #[MapRequestPayload(resolver: RequestFormPayloadValueResolver::class)] ?CommutatorForm $commutatorForm = null
    ): Response
    {
        if($request->isMethod(Request::METHOD_POST)){
            $commutatorForm->transform($provinceRepository, $municipalityRepository);
        }

        $form = $this->createForm(CommutatorType::class, $commutatorForm);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){
            dump($commutatorForm);
            die();
        }

        return $this->render('conection/direct.html.twig', [
            'controller_name' => 'ConectionController',
            'form' => $form,
            'commutator' => $form->getData()
        ]);
    }

}
