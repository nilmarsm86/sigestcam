<?php

namespace App\Controller;

use App\Controller\Traits\MunicipalityTrait;
use App\Entity\Msam;
use App\Form\MsamType;
use App\Repository\MsamRepository;
use App\Service\CrudActionService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/msam')]
#[IsGranted('ROLE_BOSS')]
class MsamController extends AbstractController
{
    use MunicipalityTrait;

    #[Route('/', name: 'msam_index', methods: ['GET'])]
    public function index(Request $request, MsamRepository $msamRepository, CrudActionService $crudActionService): Response
    {
        $template = $crudActionService->indexAction($request, $msamRepository, 'findMsams', 'msam');
        return new Response($template);
    }

    #[Route('/new', name: 'msam_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $msam = new Msam();
        $form = $this->createForm(MsamType::class, $msam, [
            'action' => $this->generateUrl('msam_new'),
            'crud' => true
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $dataAddress = $request->request->all()['msam']['address'];
            $municipalityId = $dataAddress['municipality'] ?? null;
            $municipality = $this->findMunicipalityForEquipment($entityManager, $municipalityId);

            $msam->setMunicipality($municipality);
            $msam->deactivate();

            $entityManager->persist($msam);
            $entityManager->flush();

            //si el formulario se mando correctamente por ajax
            if($request->isXmlHttpRequest()){
                return $this->render("partials/_form_success.html.twig", [
                    'id' => 'new_msam_'.$msam->getId(),
                    'type' => 'text-bg-success',
                    'message' => 'Se ha agregado un msam.'
                ]);
            }

            $this->addFlash('success', 'Se ha agregado un msam.');
            return $this->redirectToRoute('msam_index', [], Response::HTTP_SEE_OTHER);
        }

        $template = ($request->isXmlHttpRequest()) ? '_form.html.twig' : 'new.html.twig';

        return $this->render("msam/$template", [
            'msam' => $msam,
            'form' => $form,
            'title' => 'Nuevo msam'
        ]);
    }

    #[Route('/{id}', name: 'msam_show', methods: ['GET'])]
    public function show(Request $request, Msam $msam, CrudActionService $crudActionService): Response
    {
        $template = $crudActionService->showAction($request, $msam, 'msam', 'msam', 'Detalles del msam');
        return new Response($template);
    }

    #[Route('/{id}/edit', name: 'msam_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Msam $msam, EntityManagerInterface $entityManager): Response
    {
        $postData = $request->request->all();

        $provinceId = isset($postData['msam'])
            ? $request->request->all()['msam']['address']['province']
            : $msam->getMunicipality()->getProvince()->getId();

        $municipalityId = $this->findMunicipalityForExistEquipment($msam, $request, 'msam');

        $form = $this->createForm(MsamType::class, $msam, [
            'action' => $this->generateUrl('msam_edit', ['id' => $msam->getId()]),
            'province' => (int) $provinceId,
            'municipality' => (int) $municipalityId,
            'crud' => true
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $municipality = $this->findMunicipalityForEquipment($entityManager, $municipalityId);
            $msam->setMunicipality($municipality);
            $entityManager->flush();

            //si el formulario se mando correctamente por ajax
            if($request->isXmlHttpRequest()){
                return $this->render("partials/_form_success.html.twig", [
                    'id' => 'edit_msam_'.$msam->getId(),
                    'type' => 'text-bg-success',
                    'message' => 'Se ha modificado el msam.'
                ]);
            }

            $this->addFlash('success', 'Se ha modificado el msam.');
            return $this->redirectToRoute('msam_index', [], Response::HTTP_SEE_OTHER);
        }

        $template = ($request->isXmlHttpRequest()) ? '_form.html.twig' : 'edit.html.twig';

        return $this->render("msam/$template", [
            'msam' => $msam,
            'form' => $form,
            'title' => 'Editar msam'
        ]);
    }

    /*#[Route('/{id}', name: 'msam_delete', methods: ['POST'])]
    public function delete(Request $request, Msam $msam, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$msam->getId(), $request->request->get('_token'))) {
            $entityManager->remove($msam);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_msam_index', [], Response::HTTP_SEE_OTHER);
    }*/

    #[Route('/state', name: 'msam_state', methods: ['POST'])]
    public function state(Request $request, MsamRepository $msamRepository, CrudActionService $crudActionService): Response
    {
        $template = $crudActionService->stateAction($request, $msamRepository, 'Se ha desactivado el Msam.', 'Se ha activado el Msam.');
        return new Response($template);
    }

    #[Route('/card/{id}', name: 'msam_card', requirements: ['id' => '\d+'], methods: ['GET'])]
    public function port(Request $request, Msam $msam): Response
    {
        $template = ($request->isXmlHttpRequest()) ? '_port_detail.html.twig' : 'port.html.twig';

        return $this->render("commutator/$template", [
            'msam' => $msam,
            'title' => 'Targetas del msam',
            //'forSelect' => PortType::forSelect(),
        ]);
    }
}
