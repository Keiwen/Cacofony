<?php

namespace Keiwen\Cacofony\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Debug\Exception\FlattenException;
use Symfony\Component\HttpKernel\Log\DebugLoggerInterface;
use Symfony\Component\HttpFoundation\Response;

class ExceptionController extends \Symfony\Bundle\TwigBundle\Controller\ExceptionController
{


    /**
     * {@inheritdoc}
     */
    public function showAction(Request $request, FlattenException $exception, DebugLoggerInterface $logger = null)
    {
        $currentContent = $this->getAndCleanOutputBuffering($request->headers->get('X-Php-Ob-Level', -1));
        $showException = $request->attributes->get('showException', $this->debug); // As opposed to an additional parameter, this maintains BC

        //add sub function here to handle/modify exception if needed
        $this->handleException($exception, $request);

        $code = $exception->getStatusCode();

        //add status code to generated response
        return new Response($this->twig->render(
            (string) $this->findTemplate($request, $request->getRequestFormat(), $code, $showException),
            array(
                'status_code' => $code,
                'status_text' => isset(Response::$statusTexts[$code]) ? Response::$statusTexts[$code] : '',
                'exception' => $exception,
                'logger' => $logger,
                'currentContent' => $currentContent,
                'fullUri' => $request->getSchemeAndHttpHost() . $request->getRequestUri(),
            )
        ), $code);
    }


    /**
     * Override this to handle or modify incoming exception / request
     * @param FlattenException $exception
     * @param Request          $request
     */
    protected function handleException(FlattenException &$exception, Request &$request)
    {
        //if we get a twig runtime error, try to get previous exception that could cause this one
        if($exception->getClass() == \Twig_Error_Runtime::class && !empty($exception->getPrevious())) {
            $exception = $exception->getPrevious();
        }
    }
}
