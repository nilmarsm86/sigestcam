<?php

namespace App\EventSubscriber;

use App\Components\Live\ConnectionDetailEditInline;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\UX\TwigComponent\Event\PreRenderEvent;

class ComponentPreRenderSubscriber implements EventSubscriberInterface
{
    public function onPreRender(PreRenderEvent $event): void
    {
        if($event->getComponent() instanceof ConnectionDetailEditInline){
            $event->setTemplate($event->getComponent()->template);
        }
    }

    public static function getSubscribedEvents(): array
    {
        return [PreRenderEvent::class => 'onPreRender'];
    }
}