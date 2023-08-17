<?php

namespace App\Controller;

use App\Entity\Municipality;
use App\Entity\Province;
use App\Form\CommutatorType;
use App\Resolver\RequestFormPayloadValueResolver;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/commutator', name: 'commutator')]
class CommutatorController2 extends AbstractController
{
    #[Route('/', name: '_list')]
    public function index(): Response
    {
        return $this->render('commutator/index.html.twig', [
            'controller_name' => 'CommutatorController',
        ]);
    }

    /*#[Route('/new', name: '_new')]
    public function new(Request $request,
        #[MapRequestPayload(
            serializationContext: [
                'entities'=>[Province::class, Municipality::class],
                'form' => 'commutator'
            ],
            resolver: RequestFormPayloadValueResolver::class,
        )]
        ?CameraFormModel        $commutatorForm = null,
        bool                    $fromComponent = false
    ): Response
    {
        $form = $this->createForm(CommutatorType::class, $commutatorForm);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){
            dump($commutatorForm);
            //die();
        }

        return $this->render($fromComponent ? 'commutator/_form_fields.html.twig' : 'commutator/new.html.twig', [
            'controller_name' => 'CommutatorController',
            'form' => $form,
            'commutator' => $commutatorForm,
        ]);
    }

    public function test($datp)
    {
        dump("OK");
        die();
    }*/
}
