<?php

namespace App\Service;

use App\DTO\Paginator;
use App\Entity\Enums\State;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Twig\Environment;

class CrudActionService
{
    public function __construct(private readonly Environment $environment)
    {
    }

    public function indexAction(Request $request, ServiceEntityRepository $repository, string $findMethod, string $templateDir, array $vars = []): string
    {
        $filter = $request->query->get('filter', '');
        $amountPerPage = $request->query->get('amount', 10);
        $pageNumber = $request->query->get('page', 1);

        $data = call_user_func_array([$repository, $findMethod], [$filter, $amountPerPage, $pageNumber]);

        $template = ($request->isXmlHttpRequest()) ? '_list.html.twig' : 'index.html.twig';

        return $this->environment->render("$templateDir/$template", [
            'filter' => $filter,
            'paginator' => new Paginator($data, $amountPerPage, $pageNumber)
        ]+$vars);
    }

    public function showAction(Request $request, object $entity, string $templateDir, string $type, string $title, array $vars = []): string
    {
        $template = ($request->isXmlHttpRequest()) ? '_detail.html.twig' : 'show.html.twig';

        return $this->environment->render("$templateDir/$template", [
            $type => $entity,
            'title' => $title
        ]+$vars);
    }

    public function stateAction(Request $request, ServiceEntityRepository $repository, string $deactivateMessage, string $activateMessage): string
    {
        if($request->isXmlHttpRequest()){
            $id = $request->request->get('id');
            $entity = $repository->find($id);

            $stateId = $request->request->get('state');
            $state = State::from($stateId);

            ($state->name === State::Active->name) ? $entity->activate() : $entity->deactivate();

            $repository->save($entity, true);
            return $this->environment->render("partials/_form_success.html.twig", [
                'id' => 'state_'.$stateId.'-'.$entity->getId(),
                'type' => 'text-bg-success',
                'message' => ($stateId === "0") ? $deactivateMessage : $activateMessage
            ]);
        }

        throw new BadRequestHttpException('Ajax request');
    }
}