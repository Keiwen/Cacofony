<?php

namespace Keiwen\Cacofony\WidgetBundle\EventListener;

use Keiwen\Cacofony\WidgetBundle\Controller\WidgetController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\FilterResponseEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * AsyncWidgetListener injects the async widget calls.
 *
 * Calls only injected on well-formed HTML (with a proper </body> tag).
 * This means that its never included in sub-requests or ESI requests.
 */
class AsyncWidgetListener implements EventSubscriberInterface
{

    protected $twig;
    protected $widgetController;


    /**
     * AsyncWidgetListener constructor.
     *
     * @param WidgetController  $widgetController
     * @param \Twig_Environment $twig
     */
    public function __construct(WidgetController $widgetController, \Twig_Environment $twig)
    {
        $this->widgetController = $widgetController;
        $this->twig = $twig;
    }


    /**
     * @param FilterResponseEvent $event
     */
    public function onKernelResponse(FilterResponseEvent $event)
    {
        $response = $event->getResponse();
        $request = $event->getRequest();

        if (!$event->isMasterRequest()) {
            return;
        }
        // do not capture redirects or modify XML HTTP Requests
        if ($request->isXmlHttpRequest()) {
            return;
        }

        if($response->isRedirection()
            || ($response->headers->has('Content-Type') && false === strpos($response->headers->get('Content-Type'), 'html'))
            || 'html' !== $request->getRequestFormat()
            || false !== stripos($response->headers->get('Content-Disposition'), 'attachment;')
        ) {
            return;
        }

        $this->injectCalls($response);
    }

    /**
     * Injects the widget calls into the given Response.
     * @param Response $response
     */
    protected function injectCalls(Response $response)
    {
        $calledWidgets = $this->widgetController->retrieveAsyncWidgetsCalled();
        //no injection if no widget called
        if(empty($calledWidgets)) return;

        $content = $response->getContent();
        //inject template before closing body tag
        $pos = strripos($content, '</body>');
        if (false !== $pos) {
            $launcher = "\n".str_replace("\n", '', $this->twig->render(
                'KeiwenCacofonyWidgetBundle:AsyncWidget:_launcher.html.twig',
                array('widgetsCalled' => $calledWidgets)
            ))."\n";
            $content = substr($content, 0, $pos).$launcher.substr($content, $pos);
            $response->setContent($content);
        }
    }

    /**
     * Priority should be less than WebDebugToolbar.
     * This will trigger after, allowing WDT to register ajax call made
     * @see \Symfony\Bundle\WebProfilerBundle\EventListener\WebDebugToolbarListener
     * @return array
     */
    public static function getSubscribedEvents()
    {
        return array(
            KernelEvents::RESPONSE => array('onKernelResponse', -256),
        );
    }
}
