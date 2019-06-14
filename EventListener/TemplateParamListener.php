<?php

namespace Keiwen\Cacofony\EventListener;


use Keiwen\Cacofony\Configuration\TemplateParam;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\ResponseEvent;
use Symfony\Component\HttpKernel\Event\ViewEvent;
use Symfony\Component\HttpKernel\KernelEvents;

class TemplateParamListener implements EventSubscriberInterface
{

    public static function getSubscribedEvents()
    {
        return array(
            KernelEvents::VIEW => array(array('onKernelView', 30)),
        );
    }


    /**
     * Called after each controller. Store template parameters annotated in controller
     * @param ViewEvent $event
     */
    public function onKernelView(ViewEvent $event)
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
