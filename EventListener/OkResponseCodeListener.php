<?php

namespace Keiwen\Cacofony\EventListener;


use Keiwen\Cacofony\Configuration\OkResponseCode;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\FilterControllerEvent;
use Symfony\Component\HttpKernel\Event\FilterResponseEvent;
use Symfony\Component\HttpKernel\Event\GetResponseForControllerResultEvent;
use Symfony\Component\HttpKernel\KernelEvents;

/**
 * Class OkResponseCodeOverwriteListener
 * Listen to ok response code set by annotation
 * @see OkResponseCode
 *
 *
 * @package Keiwen\Cacofony\EventListener
 */
class OkResponseCodeListener implements EventSubscriberInterface
{

    public static function getSubscribedEvents()
    {
        return array(
            KernelEvents::RESPONSE => 'onKernelResponse',
        );
    }

    public function onKernelResponse(FilterResponseEvent $event)
    {
        $request = $event->getRequest();
        /** @var OkResponseCode $annotation */
        $annotation = $request->attributes->get('_okResponseCode');
        if(empty($annotation)) return;

        $response = $event->getResponse();
        if($response->getStatusCode() == 200) {
            $response->setStatusCode($annotation->getCode());
        }
    }

}
