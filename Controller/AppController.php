<?php

namespace Keiwen\Cacofony\Controller;

use Keiwen\Cacofony\FormProcessor\DefaultFormProcessor;
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
     * @param string $suffix
     * @return string
     */
    protected function getAutodumpParameterName(string $suffix = '')
    {
        $config = $this->getConfiguration();
        return $config['autodump_parameter'] . $suffix;
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
            $paramName = $this->getAutodumpParameterName();
            dump(array($paramName => $templateParams));
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
        $config = $this->getConfiguration();
        return $this->getRequest()->getCookie($config['cookie_disclaimer'], StringSanitizer::FILTER_BOOLEAN, false);
    }


    /**
     * @param bool
     */
    public function setCookieDisclaimer(bool $accepted)
    {
        $config = $this->getConfiguration();
        $this->getResponse()->setCookie($config['cookie_disclaimer'], $accepted);
    }



    /**
     * @param string $processorClass (must extends DefaultFormProcessor)
     * @param array  $defautData
     * @param array  $formOptions
     * @return DefaultFormProcessor
     */
    public function createFormProcessor(string $processorClass, array $defautData = array(), array $formOptions = array())
    {
        if(!is_subclass_of($processorClass, DefaultFormProcessor::class)) {
            return null;
        }
        return new $processorClass($this->container->get('form.factory'), $defautData, $formOptions);
    }


    /**
     * @param string $formClass
     * @param        $entity
     * @param array  $options
     * @return \Symfony\Component\Form\Form
     */
    public function createEntityForm(string $formClass, $entity, array $options = array())
    {
        return $this->createForm($formClass, $entity, $options);
    }


}
