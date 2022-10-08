<?php

namespace Keiwen\Cacofony\EventListener;


use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\ResponseEvent;
use Symfony\Component\HttpKernel\Event\ViewEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Twig\Environment as TwigEnvironment;

class AutoDumpListener implements EventSubscriberInterface
{

    protected $toDump = array();
    protected $twig;
    protected $parameterName = '';

    const SUBPART_TWIG_GLOBALS = '_twig_globals';


    /**
     * AutoDumpListener constructor.
     *
     * @param string                 $parameterName empty will disable autodump
     * @param TwigEnvironment|null   $twig
     */
    public function __construct(string $parameterName = '', TwigEnvironment $twig = null)
    {
        $this->parameterName = $parameterName;
        $this->twig = $twig;
    }

    public static function getSubscribedEvents()
    {
        return array(
            KernelEvents::VIEW => array(array('onKernelView', 20)),
            KernelEvents::RESPONSE => array(array('onKernelResponse', 20)),
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
     * @param ViewEvent $event
     */
    public function onKernelView(ViewEvent $event)
    {
        if(!function_exists('dump') || empty($this->getAutodumpParameterName())) return;
        $parameters = $event->getControllerResult();
        $request = $event->getRequest();
        /** @var Template $template */
        $template = $request->attributes->get('_template');
        if(empty($template)) return;

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
     * @param ResponseEvent $event
     */
    public function onKernelResponse(ResponseEvent $event)
    {
        if(!function_exists('dump') || empty($this->getAutodumpParameterName())) return;
        //dump only for master request if not empty
        if($event->isMainRequest() && !empty($this->toDump)) {
            //add twig globals
            if(!empty($this->twig)) {
                $this->toDump[static::SUBPART_TWIG_GLOBALS] = $this->twig->getGlobals();
            }
            dump(array($this->getAutodumpParameterName() => $this->toDump));
        }
    }

}
