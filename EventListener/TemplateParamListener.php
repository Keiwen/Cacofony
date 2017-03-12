<?php

namespace Keiwen\Cacofony\EventListener;


use Keiwen\Cacofony\Configuration\TemplateParam;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\GetResponseForControllerResultEvent;
use Symfony\Component\HttpKernel\KernelEvents;

class TemplateParamListener implements EventSubscriberInterface
{

    public static function getSubscribedEvents()
    {
        return array(
            KernelEvents::VIEW => 'onKernelView',
        );
    }


    /**
     * Called after each controller. Store template parameters annotated in controller
     * @param GetResponseForControllerResultEvent $event
     */
    public function onKernelView(GetResponseForControllerResultEvent $event)
    {
        $parameters = $event->getControllerResult();
        if(!is_array($parameters)) return;
        $request = $event->getRequest();
        /** @var TemplateParam[] $templateParams */
        $templateParams = $request->attributes->get('_'.TemplateParam::ALIAS_NAME, array());
        foreach($templateParams as $templateParam) {
            if(!isset($parameters[$templateParam->getValue()])) {
                $parameters[$templateParam->getValue()] = $templateParam->getParamValue();
            }
        }
        $event->setControllerResult($parameters);
    }


}
