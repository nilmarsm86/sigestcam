<?php

namespace App\Components\Twig;


use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

#[AsTwigComponent(template: 'components/twig/breadcrumb.html.twig')]
final class Breadcrumb
{
    public array $options;
    public string $current = '';
}
