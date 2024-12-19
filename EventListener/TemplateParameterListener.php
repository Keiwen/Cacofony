<?php

namespace Keiwen\Cacofony\EventListener;


use Keiwen\Cacofony\Configuration\TemplateParameter;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Component\HttpKernel\Event\ViewEvent;
use Symfony\Component\HttpKernel\Event\ControllerEvent;
use Symfony\Component\HttpKernel\KernelEvents;

class TemplateParameterListener
{

    /**
     * Called after each controller that does not return a Response.
     * Store template parameters annotated in controller
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


    /**
     * Called just before each controller.
     * Store template parameters annotated in request
     * @param ControllerEvent $event
     */
    #[AsEventListener(event: KernelEvents::CONTROLLER)]
    public function onKernelController(ControllerEvent $event)
    {
        $controller = $event->getController();
        if(!is_array($controller)) return;
        [$object, $method] = $controller;

        $requestAttributes = $event->getRequest()->attributes->get(TemplateParameter::REQUEST_ATTRIBUTE_NAME);

        // use reflection to get template param attrributes
        try {
            // ON CONTROLLER CLASS ITSELF
            $reflectionClass = new \ReflectionClass($object);
            $reflectionAttributes = $reflectionClass->getAttributes(TemplateParameter::class);
            foreach ($reflectionAttributes as $reflectionAttribute) {
                $templateParameter = $reflectionAttribute->newInstance();
                $requestAttributes[] = $templateParameter;
            }

            // ON CONTROLLER ACTION
            $reflectionMethod = new \ReflectionMethod($object, $method);
            $reflectionAttributes = $reflectionMethod->getAttributes(TemplateParameter::class);
            foreach ($reflectionAttributes as $reflectionAttribute) {
                $templateParameter = $reflectionAttribute->newInstance();
                $requestAttributes[] = $templateParameter;
            }

            // STORE ALL IN REQUEST
            $event->getRequest()->attributes->set(TemplateParameter::REQUEST_ATTRIBUTE_NAME, $requestAttributes);
        } catch (\Exception $e) {
            // do nothing
        }
    }


}
