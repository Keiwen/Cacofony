<?php

namespace Keiwen\Cacofony\Controller;

use Keiwen\Cacofony\Http\Response;
use Keiwen\Utils\Sanitize\StringSanitizer;
use Symfony\Component\HttpFoundation\RedirectResponse;

class AppController extends DefaultController
{

    protected $templateParams = array();

    /** @var  Response $response */
    protected $response;

    const FMSG_ERROR = 'error';
    const FMSG_INFO = 'info';
    const FMSG_SUCCESS = 'success';
    const FMSG_WARNING = 'warning';


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
     * @return boolean
     */
    public static function canDump()
    {
        return function_exists('dump') ;
    }


    /**
     * use this method with template annotations to render a view
     * @see http://symfony.com/doc/current/bundles/SensioFrameworkExtraBundle/annotations/view.html
     * @return array template params
     */
    protected function renderTemplate()
    {
        $templateParams = $this->getTemplateParams();

        $config = $this->getConfiguration();
        //dump everything on dev if asked
        if(static::canDump() && $config['autodump']) {
            dump(array($config['autodump_parameter'] => $templateParams));
        }
        return $templateParams;
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
    public function redirect($url, $status = 302)
    {
        return $this->getResponse()->generateRedirect($url, $status);
    }


    /**
     * @return bool
     */
    public function getCookieDisclaimer()
    {
        return $this->getRequest()->getCookie($this->getCookieDisclaimerName(), StringSanitizer::FILTER_BOOLEAN, false);
    }

    /**
     * @return string
     */
    public function getCookieDisclaimerName()
    {
        $config = $this->getConfiguration();
        return $config['cookie_disclaimer'];
    }


    /**
     * @param bool
     */
    public function setCookieDisclaimer(bool $accepted)
    {
        $this->getResponse()->setCookie($this->getCookieDisclaimerName(), $accepted);
    }




}
