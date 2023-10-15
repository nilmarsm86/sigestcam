<?php

namespace App\Controller;

use App\DTO\Paginator;
use App\Entity\Enums\Priority;
use App\Entity\Enums\ReportState;
use App\Entity\Report;
use App\Form\ReportType;
use App\Form\Types\PriorityEnumType;
use App\Repository\ReportRepository;
use App\Service\CrudActionService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Enums\ReportType as Type;

#[Route('/report')]
class ReportController extends AbstractController
{
    #[Route('/', name: 'report_index', methods: ['GET'])]
    public function index(Request $request, ReportRepository $reportRepository, CrudActionService $crudActionService): Response
    {
        $filter = $request->query->get('filter', '');
        $amountPerPage = $request->query->get('amount', 10);
        $pageNumber = $request->query->get('page', 1);

        $priority = $request->query->get('priority', '');
        $state = $request->query->get('state', '');
        $type = $request->query->get('type', '');

        $data = $reportRepository->findReports($filter, $amountPerPage, $pageNumber, $priority, $state, $type);

        $template = ($request->isXmlHttpRequest()) ? '_list.html.twig' : 'index.html.twig';

        return $this->render("report/$template", [
            'filter' => $filter,
            'paginator' => new Paginator($data, $amountPerPage, $pageNumber),
            'priorities' => Priority::cases(),
            'states' => ReportState::cases(),
            'types' => Type::cases()
        ]);
    }

    #[Route('/new', name: 'report_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager, bool $modal = false): Response
    {
        $report = new Report();
        $form = $this->createForm(ReportType::class, $report,[
            'action' => $this->generateUrl('report_new'),
            'equipment' => (int) $request->query->get('equipment')
            //deberia pasar el rol auenticado y ponerlo en un campo oculto,
            //de esta forma puedo validar que el rol autenticado tiene los permisos correspondientes
            //sino muestro mensaje de error en el formulario
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $report->setManagementOfficer($this->getUser());//verificar que el usuario autenticado tiene ese rol
            $entityManager->persist($report);
            $entityManager->flush();

            //si el formulario se mando correctamente por ajax
            if($request->isXmlHttpRequest()){
                return $this->render("partials/_form_success.html.twig", [
                    'id' => 'new_report_'.$report->getId(),
                    'type' => 'text-bg-success',
                    'message' => 'Se ha creado el reporte con el número '.$report->getNumber()
                ]);
            }

            $this->addFlash('success', 'Se ha creado el reporte con el número '.$report->getNumber());
            return $this->redirectToRoute('app_report_index', [], Response::HTTP_SEE_OTHER);
        }

        if($request->isXmlHttpRequest()){
            $template = '_form_fields.html.twig';
        }else{
            $template = ($modal) ? '_form_fields.html.twig' : 'new.html.twig';
        }

        return $this->render("report/$template", [
            'report' => $report,
            'form' => $form,
            'title' => 'Nuevo reporte',
            'modal' => $modal
        ]);
    }

    #[Route('/{id}', name: 'report_show', requirements: ['id' => '\d+'], methods: ['GET'])]
    public function show(Request $request, Report $report, CrudActionService $crudActionService): Response
    {
        $template = $crudActionService->showAction($request, $report, 'report', 'report', 'Detalles del reporte');
        return new Response($template);
    }

    #[Route('/{id}/edit', name: 'report_edit', requirements: ['id' => '\d+'], methods: ['GET', 'POST'])]
    public function edit(Request $request, Report $report, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(ReportType::class, $report, [
            'action' => $this->generateUrl('report_edit', ['id' => $report->getId()]),
            'equipment' => $report->getEquipment()->getId(),
            'existReport' => true
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            if($form->isValid()){
                $report->close();
                $entityManager->flush();

                //si el formulario se mando correctamente por ajax
                if($request->isXmlHttpRequest()){
                    return $this->render("partials/_form_success.html.twig", [
                        'id' => 'edit_report_'.$report->getId(),
                        'type' => 'text-bg-success',
                        'message' => 'Se ha '.($report->isOpen() ? 'modificado' : 'cerrado').' el reporte con el número '.$report->getNumber()
                    ]);
                }

                $this->addFlash('success', 'Se ha '.($report->isOpen() ? 'modificado' : 'cerrado').' el reporte con el número '.$report->getNumber());
                return $this->redirectToRoute('report_index', ['state' => 0], Response::HTTP_SEE_OTHER);
            }else{
                $template = ($request->isXmlHttpRequest()) ? '_form_fields.html.twig' : 'edit.html.twig';

                return $this->render("report/$template", [
                    'report' => $report,
                    'form' => $form,
                    'title' => 'Editar reporte',
                    'modal' => false
                ]);
            }
        }

        $template = ($request->isXmlHttpRequest()) ? '_form.html.twig' : 'edit.html.twig';

        return $this->render("report/$template", [
            'report' => $report,
            'form' => $form,
            'title' => 'Editar reporte',
            'modal' => false
        ]);
    }

    #[Route('/{id}', name: 'report_delete', methods: ['POST'])]
    public function delete(Request $request, Report $report, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$report->getId(), $request->request->get('_token'))) {
            $entityManager->remove($report);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_report_index', [], Response::HTTP_SEE_OTHER);
    }
}
