<?php

namespace App\Components\Twig;


use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

#[AsTwigComponent(template: 'components/twig/modal_form.html.twig')]
final class ModalForm
{
    public string $title;
    public string $src;
    public array $vars = [];
    public string $id;
    public string $type = 'controller';
}
