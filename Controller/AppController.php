<?php

namespace Keiwen\Cacofony\Controller;

use Keiwen\Cacofony\Configuration\TemplateParameter;
use Keiwen\Cacofony\DependencyInjection\KeiwenCacofonyExtension;
use Keiwen\Cacofony\EventListener\AutoDumpListener;
use Keiwen\Cacofony\Http\Response;
use Keiwen\Utils\Analyser\DebugBacktracer;
use Keiwen\Utils\Format\StringFormat;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Response as SymfonyResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Exception\RouteNotFoundException;

class AppController extends DefaultController
{

    protected $templateParameters = array();

    /** @var  Response $response */
    protected $response;


    public function __construct()
    {
        $this->response = new Response();
    }

    /**
     * @return Response
     */
    protected function getResponse()
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
        $templateParameters = TemplateParameter::getArrayFromRequest($request);

        //merge template param from attributes, then params given by method,
        //and finally parameters sent in render method. Last have priority.
        $mergedParameters = array_merge($templateParameters, $this->getTemplateParameters(), $parameters);

        // get calling controller trace
        $controllerTrace = DebugBacktracer::getCallersTrace(1);
        $controllerTrace = reset($controllerTrace);
        // we got the namespaced name, get the end and remove 'controller' from name
        $controllerName = explode('\\', $controllerTrace['class']);
        $controllerName = end($controllerName);
        $controllerName = str_replace('Controller', '', $controllerName);
        // Regarding function, remove 'Action' at the end if needed
        $actionName = $controllerTrace['function'];
        $actionName = str_replace('Action', '', $actionName);

        //snake case names when looking for template
        $stringFormatter = new StringFormat();
        $templatePath = $stringFormatter->formatSnakeCase($controllerName);
        $templateName = $stringFormatter->formatSnakeCase($actionName);

        $templateExtension = $this->getParameter(KeiwenCacofonyExtension::TEMPLATE_GUESSER_EXTENSION);

        $view = $templatePath . '/' . $templateName. '.' . $templateExtension;

        return $this->render($view, $mergedParameters, $this->getResponse());
    }



    /**
     * @return array
     */
    protected function getTemplateParameters()
    {
        return $this->templateParameters;
    }


    /**
     * @param string $name
     * @param mixed $value
     * @param bool $overwrite
     * @return bool
     */
    protected function addTemplateParameter(string $name, $value, bool $overwrite = false)
    {
        if(!$overwrite && isset($this->templateParameters[$name])) {
            return false;
        }
        $this->templateParameters[$name] = $value;
        return true;
    }



    /**
     * Redirect from own response as we could have stored some cookies in it
     * @param string $url
     * @param int $status
     * @return RedirectResponse
     */
    protected function redirect(string $url, int $status = Response::HTTP_FOUND): RedirectResponse
    {
        return $this->getResponse()->generateRedirect($url, $status);
    }


    /**
     * Redirect after a post (PRG pattern => post redirect get, to avoid refresh to re-submit form)
     * @param string $url
     * @return RedirectResponse
     */
    protected function redirectAfterPost($url): RedirectResponse
    {
        return $this->redirect($url, Response::HTTP_SEE_OTHER);
    }


    /**
     * @param Request|null $request
     * @param int          $status
     * @return RedirectResponse
     * @throws RouteNotFoundException
     */
    protected function redirectToReferer(?Request $request = null, $status = Response::HTTP_SEE_OTHER): RedirectResponse
    {
        if(empty($request)) $request = $this->getRequest();
        $referer = $request->headers->get('referer');
        if(empty($referer)) throw new RouteNotFoundException('Unable to redirect to referer: cannot found referer in request headers');
        return $this->redirect($referer, $status);
    }

    /**
     * @param string $type
     * @param $data
     * @param array $options
     * @return FormInterface
     */
    protected function createHandledForm(string $type, $data = null, array $options = []): FormInterface
    {
        $form = $this->createForm($type, $data, $options);
        $form->handleRequest($this->getRequest());
        return $form;
    }

}
