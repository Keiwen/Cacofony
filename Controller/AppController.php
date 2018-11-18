<?php

namespace Keiwen\Cacofony\Controller;

use Keiwen\Cacofony\Http\Response;
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
     * use this method with template annotations to render a view
     * @see http://symfony.com/doc/current/bundles/SensioFrameworkExtraBundle/annotations/view.html
     * @return array template params
     */
    protected function renderTemplate()
    {
        return $this->getTemplateParams();
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
