<?php

namespace Keiwen\Cacofony\EventListener;


use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\FilterResponseEvent;
use Symfony\Component\HttpKernel\Event\GetResponseForControllerResultEvent;
use Symfony\Component\HttpKernel\KernelEvents;

class AutoDumpListener implements EventSubscriberInterface
{

    protected $toDump = array();
    protected $twig;
    protected $parameterName = '';

    const SUBPART_TWIG_GLOBALS = '_twig_globals';
    const SUBPART_WIDGET = '_widget';


    /**
     * AutoDumpListener constructor.
     *
     * @param string                 $parameterName empty will disable autodump
     * @param \Twig_Environment|null $twig
     */
    function __construct(string $parameterName = '', \Twig_Environment $twig = null)
    {
        $this->parameterName = $parameterName;
        $this->twig = $twig;
    }

    public static function getSubscribedEvents()
    {
        return array(
            KernelEvents::VIEW => 'onKernelView',
            KernelEvents::RESPONSE => 'onKernelResponse',
        );
    }

    /**
     * @return string
     */
    protected function getAutodumpParameterName()
    {
        return $this->parameterName;
    }


    /**
     * Called after each controller. Store parameters send by controller
     * @param GetResponseForControllerResultEvent $event
     */
    public function onKernelView(GetResponseForControllerResultEvent $event)
    {
        if(!function_exists('dump') || empty($this->getAutodumpParameterName())) return;
        $parameters = $event->getControllerResult();
        $request = $event->getRequest();
        /** @var Template $template */
        $template = $request->attributes->get('_template');

        if($template instanceof Template) {
            $template = $template->getTemplate();
            if(!is_string($template)) $template = $template->getLogicalName();
        }

        $this->addParameterToDump($template, $parameters);
    }


    /**
     * @param string $template
     * @param mixed  $parameters
     * @param string $subPart
     */
    public function addParameterToDump(string $template, $parameters, string $subPart = '')
    {
        //Same template could be called several time, so keep an array for each template call
        if(empty($subPart)) {
            $this->toDump[$template][] = $parameters;
        } else {
            $this->toDump[$subPart][$template][] = $parameters;
        }
    }


    /**
     * Called for each request. Use only the master request to dump all stored parameters
     * @param FilterResponseEvent $event
     */
    public function onKernelResponse(FilterResponseEvent $event)
    {
        if(!function_exists('dump') || empty($this->getAutodumpParameterName())) return;
        //Do not dump if empty
        if($event->isMasterRequest() && !empty($this->toDump)) {
            //add twog globals
            if(!empty($this->twig)) {
                $this->toDump[static::SUBPART_TWIG_GLOBALS] = $this->twig->getGlobals();
            }
            dump(array($this->getAutodumpParameterName() => $this->toDump));
        }
    }

}
