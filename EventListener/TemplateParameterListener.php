<?php

namespace Keiwen\Cacofony\EventListener;


use Keiwen\Cacofony\Configuration\TemplateParameter;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Component\HttpKernel\Event\ResponseEvent;
use Symfony\Component\HttpKernel\Event\ViewEvent;
use Symfony\Component\HttpKernel\KernelEvents;

class TemplateParameterListener
{

    /**
     * Called after each controller. Store template parameters annotated in controller
     * @param ViewEvent $event
     */
    #[AsEventListener(event: KernelEvents::VIEW, priority: 30)]
    public function onKernelView(ViewEvent $event)
    {
        $parameters = $event->getControllerResult();
        if(!is_array($parameters)) return;
        $request = $event->getRequest();
        $templateParameters = TemplateParameter::getArrayFromRequest($request);
        $parameters = array_merge($templateParameters, $parameters);
        $event->setControllerResult($parameters);
    }


}
