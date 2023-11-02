<?php

namespace App\EventSubscriber;

use App\Components\Live\ConnectionDetailEditInline;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\UX\TwigComponent\Event\PreRenderEvent;

class ComponentPreRenderSubscriber implements EventSubscriberInterface
{
    /**
     * @param PreRenderEvent $event
     * @return void
     */
    public function onPreRender(PreRenderEvent $event): void
    {
        if($event->getComponent() instanceof ConnectionDetailEditInline){
            $event->setTemplate($event->getComponent()->template);
        }
    }

    /**
     * @return string[]
     */
    public static function getSubscribedEvents(): array
    {
        return [PreRenderEvent::class => 'onPreRender'];
    }
}