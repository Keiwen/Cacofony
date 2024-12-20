<?php

namespace Keiwen\Cacofony\EventListener;


use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Component\HttpKernel\Event\ResponseEvent;
use Symfony\Component\HttpKernel\Event\ViewEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Twig\Environment as TwigEnvironment;

class AutoDumpListener
{

    protected $toDump = array();
    protected $twig;
    protected $parameterName = '';
    protected $env = '';

    const SUBPART_TWIG_GLOBALS = '_twig_globals';


    /**
     * AutoDumpListener constructor.
     *
     * @param string                 $appEnv        application environment (dev, prod)
     * @param string                 $parameterName empty will disable autodump
     * @param TwigEnvironment|null   $twig
     */
    public function __construct(string $appEnv = '', string $parameterName = '', ?TwigEnvironment $twig = null)
    {
        $this->env = $appEnv;
        $this->parameterName = $parameterName;
        $this->twig = $twig;
    }

    /**
     * @return string
     */
    protected function getAutodumpParameterName()
    {
        return $this->parameterName;
    }

    protected function isDevEnvironment(): bool
    {
        return $this->env == 'dev';
    }


    /**
     * Called after each controller that does not return a Response.
     * Store parameters send by controller
     * @param ViewEvent $event
     */
    #[AsEventListener(event: KernelEvents::VIEW, priority: 20)]
    public function onKernelView(ViewEvent $event)
    {
        if(!function_exists('dump') || empty($this->getAutodumpParameterName()) || !$this->isDevEnvironment()) return;
        $parameters = $event->getControllerResult();
        if(!is_array($parameters)) return;
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
    #[AsEventListener(event: KernelEvents::RESPONSE, priority: 20)]
    public function onKernelResponse(ResponseEvent $event)
    {
        if(!function_exists('dump') || empty($this->getAutodumpParameterName()) || !$this->isDevEnvironment()) return;
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
