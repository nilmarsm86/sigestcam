<?php

namespace App\Controller;

use App\Controller\Traits\MunicipalityTrait;
use App\Entity\Modem;
use App\Entity\Municipality;
use App\Form\ModemType;
use App\Repository\CameraRepository;
use App\Repository\ModemRepository;
use App\Service\CrudActionService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/modem')]
#[IsGranted('ROLE_BOSS')]
class ModemController extends AbstractController
{
    use MunicipalityTrait;

    #[Route('/', name: 'modem_index', methods: ['GET'])]
    public function index(Request $request, ModemRepository $modemRepository, CrudActionService $crudActionService): Response
    {
        $template = $crudActionService->indexAction($request, $modemRepository, 'findModems', 'modem');
        return new Response($template);
    }

    #[Route('/new', name: 'modem_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $modem = new Modem();
        $form = $this->createForm(ModemType::class, $modem, [
            'action' => $this->generateUrl('modem_new'),
            'crud' => true
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $dataAddress = $request->request->all()['modem']['address'];
            $municipalityId = $dataAddress['municipality'] ?? null;
            $municipality = $this->findMunicipalityForEquipment($entityManager, $municipalityId);

            $modem->setMunicipality($municipality);
            $modem->deactivate();
            $entityManager->persist($modem);
            $entityManager->flush();

            //si el formulario se mando correctamente por ajax
            if($request->isXmlHttpRequest()){
                return $this->render("partials/_form_success.html.twig", [
                    'id' => 'new_modem_'.$modem->getId(),
                    'type' => 'text-bg-success',
                    'message' => 'Se ha agregado un modem.'
                ]);
            }

            $this->addFlash('success', 'Se ha agregado un modem.');
            return $this->redirectToRoute('modem_index', [], Response::HTTP_SEE_OTHER);
        }

        $template = ($request->isXmlHttpRequest()) ? '_form.html.twig' : 'new.html.twig';

        return $this->render("modem/$template", [
            'modem' => $modem,
            'form' => $form,
            'title' => 'Nuevo modem'
        ]);
    }

    #[Route('/{id}', name: 'modem_show', requirements: ['id' => '\d+'], methods: ['GET'])]
    public function show(Request $request, Modem $modem, CrudActionService $crudActionService): Response
    {
        $template = $crudActionService->showAction($request, $modem, 'modem', 'modem', 'Detalles del modem');
        return new Response($template);
    }

    #[Route('/{id}/edit', name: 'modem_edit', requirements: ['id' => '\d+'], methods: ['GET', 'POST'])]
    public function edit(Request $request, Modem $modem, EntityManagerInterface $entityManager): Response
    {
        $postData = $request->request->all();

        $provinceId = isset($postData['modem'])
            ? $request->request->all()['modem']['address']['province']
            : $modem->getMunicipality()->getProvince()->getId();

//        $municipalityId = isset($postData['modem'])
//            ? $request->request->all()['modem']['address']['municipality']
//            : $modem->getMunicipality()->getId();

        $municipalityId = $this->findMunicipalityForExistEquipment($modem, $request, 'modem');

        $form = $this->createForm(ModemType::class, $modem, [
            'action' => $this->generateUrl('modem_edit', ['id' => $modem->getId()]),
            'province' => (int) $provinceId,
            'municipality' => (int) $municipalityId,
            'crud' => true
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $municipality = $this->findMunicipalityForEquipment($entityManager, $municipalityId);
            $modem->setMunicipality($municipality);
            $entityManager->flush();

            //si el formulario se mando correctamente por ajax
            if($request->isXmlHttpRequest()){
                return $this->render("partials/_form_success.html.twig", [
                    'id' => 'edit_camera_'.$modem->getId(),
                    'type' => 'text-bg-success',
                    'message' => 'Se ha modificado el modem.'
                ]);
            }

            $this->addFlash('success', 'Se ha modificado el modem.');
            return $this->redirectToRoute('modem_index', [], Response::HTTP_SEE_OTHER);
        }

        $template = ($request->isXmlHttpRequest()) ? '_form.html.twig' : 'edit.html.twig';
        return $this->render("modem/$template", [
            'modem' => $modem,
            'form' => $form,
            'title' => 'Editar modem'
        ]);
    }

//    #[Route('/{id}', name: 'app_modem_delete', methods: ['POST'])]
//    public function delete(Request $request, Modem $modem, EntityManagerInterface $entityManager): Response
//    {
//        if ($this->isCsrfTokenValid('delete'.$modem->getId(), $request->request->get('_token'))) {
//            $entityManager->remove($modem);
//            $entityManager->flush();
//        }
//
//        return $this->redirectToRoute('app_modem_index', [], Response::HTTP_SEE_OTHER);
//    }

    #[Route('/state', name: 'modem_state', methods: ['POST'])]
    public function state(Request $request, ModemRepository $modemRepository, CrudActionService $crudActionService): Response
    {
        $template = $crudActionService->stateAction($request, $modemRepository, 'Se ha desactivado el modem.', 'Se ha activado el modem.');
        return new Response($template);
    }
}
