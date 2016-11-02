<?php

namespace Keiwen\Cacofony\Controller;

use Keiwen\Cacofony\Http\Response;
use Keiwen\Utils\Sanitize\StringSanitizer;
use Symfony\Component\HttpFoundation\RedirectResponse;

class AppController extends DefaultController
{

    //for flash messages without session
    use MessageManagerTrait;

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
        //store flash messages
        $this->storeMessages();
        return $this->getResponse()->generateRedirect($url, $status);
    }



    public function getMessageTypes() : array
    {
        return array(
            static::FMSG_INFO,
            static::FMSG_SUCCESS,
            static::FMSG_WARNING,
            static::FMSG_ERROR,
        );
    }

    public function getMessageDefaultType() : string
    {
        return static::FMSG_WARNING;
    }


    /**
     * @return array
     */
    protected function fetchFlashMessages()
    {
        if(!$this->hasRetrievedMessages()) $this->retrieveMessages();
        $messages = $this->getMessages();
        $this->emptyMessages();
        return $messages;
    }


    /**
     *
     */
    protected function storeMessages()
    {
        if(!$this->hasRetrievedMessages()) $this->retrieveMessages();
        $messages = $this->getMessages();
        $config = $this->getConfiguration();
        if(!empty($messages)) $this->getResponse()->setCookie($config['cookie_flash_messages'], json_encode($messages));
    }

    /**
     *
     */
    protected function retrieveMessages()
    {
        $this->retrievedMessages = true;
        $config = $this->getConfiguration();
        $messages = $this->getRequest()->getCookie($config['cookie_flash_messages'], StringSanitizer::FILTER_JSON_ARRAY, array());
        foreach($messages as $fMsg) {
            if(!empty($fMsg['message'])) {
                $type = empty($fMsg['type']) ? $this->getMessageDefaultType() : $fMsg['type'];
                $this->addMessage($fMsg['message'], $type);
            }
        }
        $this->getResponse()->removeCookie($config['cookie_flash_messages']);
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




}
