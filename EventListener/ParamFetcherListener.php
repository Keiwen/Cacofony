<?php


namespace Keiwen\Cacofony\EventListener;

use Keiwen\Cacofony\Http\Request;
use Keiwen\Cacofony\ParamFetcher\ParamFetcher;
use Doctrine\Common\Annotations\Reader;
use Keiwen\Cacofony\ParamFetcher\ParamReader;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpKernel\Event\ControllerEvent;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\KernelEvents;

class ParamFetcherListener implements EventSubscriberInterface
{
    protected $fetcherClass;
    protected $readerClass;
    protected $controllerParam;
    protected $annotationReader;
    protected $validator;

    const PARAM_FETCHER_MAIN_CLASS = ParamFetcher::class;


    public function __construct(Reader $annotationReader, ValidatorInterface $validator, string $fetcherClass, string $readerClass, string $controllerParam)
    {
        $this->fetcherClass = $fetcherClass;
        $this->readerClass = $readerClass;
        $this->controllerParam = $controllerParam;
        $this->annotationReader = $annotationReader;
        $this->validator = $validator;
    }


    /**
     * @return array
     */
    public static function getSubscribedEvents()
    {
        return array(
            KernelEvents::CONTROLLER => array(array('onKernelController', 4)),
        );
    }



    /**
     * Core controller handler.
     *
     * @param ControllerEvent $event
     *
     * @throws \InvalidArgumentException
     */
    public function onKernelController(ControllerEvent $event)
    {
        /** @var Request $request */
        $request = $event->getRequest();
        if(!$request instanceof Request) return;

        $callable = $event->getController();
        $controller = null;
        $methodName = '';
        if(is_array($callable)) list($controller, $methodName) = $callable;
        if(!$controller instanceof AbstractController) return;

        $attributeName = $this->getControllerAttributeName($controller, $methodName);
        $paramFetcher = $this->getParamFetcher($request, $controller, $methodName);
        $request->attributes->set($attributeName, $paramFetcher);
    }


    /**
     * @param AbstractController $controller
     * @return ParamReader
     */
    protected function getParamReader(AbstractController $controller)
    {
        try {
            $paramReader = new $this->readerClass($this->annotationReader, $controller);
        } catch (\Exception $e) {
            $paramReader = new ParamReader($this->annotationReader, $controller);
        }
        return $paramReader;
    }


    /**
     * @param Request    $request
     * @param AbstractController $controller
     * @param string     $methodName
     * @return ParamFetcher
     */
    protected function getParamFetcher(Request $request, AbstractController $controller, string $methodName)
    {
        $paramReader = $this->getParamReader($controller);

        try {
            $paramFetcher = new $this->fetcherClass($request, $paramReader, $methodName, $this->validator);
        } catch (\Exception $e) {
            $paramFetcher = new ParamFetcher($request, $paramReader, $methodName, $this->validator);
        }
        return $paramFetcher;
    }


    /**
     * Determines which attribute the paramFetcher should be injected as.
     * @param AbstractController $controller
     * @param string     $methodName
     * @return mixed|string
     */
    private function getControllerAttributeName(AbstractController $controller, string $methodName)
    {
        $method = new \ReflectionMethod($controller, $methodName);
        foreach($method->getParameters() as $param) {
            if ($this->isParamFetcherType($param)) {
                return $param->getName();
            }
        }
        // If there is no typehint, inject the paramFetcher using a default name.
        return $this->controllerParam;
    }


    /**
     * Returns true if the given controller parameter is type-hinted as
     * an instance of Annotation.
     * @param \ReflectionParameter $controllerParam A parameter of the controller action.
     * @return bool
     */
    private function isParamFetcherType(\ReflectionParameter $controllerParam)
    {
        $type = $controllerParam->getClass();
        if (null === $type) {
            return false;
        }
        return $type->isSubclassOf(self::PARAM_FETCHER_MAIN_CLASS);
    }
}
