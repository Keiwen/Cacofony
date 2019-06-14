<?php

namespace Keiwen\Cacofony\Controller;

use Keiwen\Cacofony\Configuration\TemplateParam;
use Keiwen\Cacofony\DependencyInjection\KeiwenCacofonyExtension;
use Keiwen\Cacofony\Http\Response;
use Keiwen\Utils\Analyser\DebugBacktracer;
use Symfony\Component\HttpFoundation\Response as SymfonyResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;

class AppController extends DefaultController
{

    protected $templateParams = array();

    /** @var  Response $response */
    protected $response;


    public function __construct()
    {
        $this->response = new Response();
    }

    /**
     * @return Response
     */
    public function getResponse()
    {
        return $this->response;
    }

    
    /**
     * Use this method without template name to get the default path automatically
     * Default path is template/{controller}/{action}
     * @return SymfonyResponse
     */
    protected function renderTemplate(array $parameters = array())
    {
        $request = $this->getRequest();
        $templateParams = TemplateParam::getArrayFromRequest($request);

        //merge template param from annotations, then params given by method,
        //and finally parameters sent in render method. Last have priority.
        $mergedParameters = array_merge($templateParams, $this->getTemplateParams(), $parameters);

        // get calling controller trace
        $controllerTrace = DebugBacktracer::getCallersTrace(1);
        $controllerTrace = reset($controllerTrace);
        // we got the namespaced name, get the end and remove 'controller' from name
        $controllerName = explode('\\', $controllerTrace['class']);
        $controllerName = end($controllerName);
        $controllerName = str_replace('Controller', '', $controllerName);

        $templateExtention = $this->getParameter(KeiwenCacofonyExtension::TEMPLATE_GUESSER_EXTENSION);
        $view = $controllerName . '/' . $controllerTrace['function']. '.' . $templateExtention;

        return $this->render($view, $mergedParameters, $this->getResponse());
    }



    /**
     * @return array
     */
    protected function getTemplateParams()
    {
        return $this->templateParams;
    }


    /**
     * @param string $name
     * @param mixed $value
     * @param bool $overwrite
     * @return bool
     */
    protected function addTemplateParam(string $name, $value, bool $overwrite = false)
    {
        if(!$overwrite && isset($this->templateParams[$name])) {
            return false;
        }
        $this->templateParams[$name] = $value;
        return true;
    }



    /**
     * Redirect from own response as we could have stored some cookies in it
     * @param string $url
     * @param int $status
     * @return RedirectResponse
     */
    public function redirect(string $url, int $status = Response::HTTP_FOUND): RedirectResponse
    {
        return $this->getResponse()->generateRedirect($url, $status);
    }


    /**
     * Redirect after a post (PRG pattern => post redirect get, to avoid refresh to re-submit form)
     * @param string $url
     * @return RedirectResponse
     */
    public function redirectAfterPost($url)
    {
        return $this->redirect($url, Response::HTTP_SEE_OTHER);
    }


    /**
     * @param Request|null $request
     * @param int          $status
     * @return RedirectResponse
     */
    public function redirectToReferer(Request $request = null, $status = Response::HTTP_SEE_OTHER)
    {
        if(empty($request)) $request = $this->getRequest();
        $referer = $request->headers->get('referer');
        return $this->redirect($referer, $status);
    }

}
