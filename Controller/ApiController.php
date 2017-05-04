<?php

namespace Keiwen\Cacofony\Controller;

use Keiwen\Cacofony\DependencyInjection\KeiwenCacofonyExtension;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class ApiController extends DefaultController
{

    protected $apiResult = array();
    protected $statusCode = 200;
    protected $statusMessage = 'OK';
    protected $location = '';

    protected $apiConfiguration;


    /**
     * @param array $headers
     * @return mixed
     */
	public function renderJson(array $headers = array())
    {
        if(!empty($this->location) && empty($headers['Location'])) $headers['Location'] = $this->location;
        return new JsonResponse($this->formatResponseData(), $this->statusCode, $headers);
	}


    /**
     * @return array
     */
    protected function getApiConfiguration()
    {
        if(empty($this->apiConfiguration)) {
            $this->apiConfiguration = $this->getParameter(KeiwenCacofonyExtension::API_PARAMETERS_CONF);
        }
        return $this->apiConfiguration;
    }



    /**
     * @return array
     */
	protected function formatResponseData()
    {
        $config = $this->getParameter(KeiwenCacofonyExtension::CONTROLLER_CONF);
        if(!$config['api_format_response']) {
            return $this->apiResult;
        }

        $apiParamsConf = $this->getApiConfiguration();
        $routeUrl = $this->getRequest()->getUrl(true, true);
        $parameters = $this->getRequest()->getRetrievedParameters();

        $formatted = array(
            $apiParamsConf['ref_url'] => $routeUrl,
            $apiParamsConf['parameters'] => $parameters,
            $apiParamsConf['result'] => $this->apiResult,
            $apiParamsConf['http_code'] => $this->statusCode,
            $apiParamsConf['message'] => $this->statusMessage,
        );
        return $formatted;
    }


    /**
     * @param int    $code
     * @param string $message
     */
	public function setResponseStatus(int $code, string $message = '')
    {
        $this->statusCode = $code;
        if(empty($message)) {
            $this->statusMessage = empty(Response::$statusTexts[$code]) ? '' : Response::$statusTexts[$code];
        } else {
            $this->statusMessage = $message;
        }
    }

    /**
     * @param string $location
     * @param string $message
     * @param int    $code
     */
    public function setResponsePostStatus(string $location, string $message = '', int $code = Response::HTTP_CREATED)
    {
        $this->location = $location;
        $this->setResponseStatus($code, $message);
    }



	/**
	 * @param string $name
	 * @param mixed $value
	 */
	protected function setApiResult(string $name, $value)
    {
		$this->apiResult[$name] = $value;
	}

	/**
	 * @param string $name
	 * @return null|mixed
	 */
	protected function getApiResult(string $name)
    {
		if($this->hasApiResult($name)) {
			return $this->apiResult[$name];
		}
		return null;
	}

	/**
	 * @param string $name
	 * @return bool
	 */
	protected function hasApiResult(string $name)
    {
		if(isset($this->apiResult[$name])) {
			return true;
		}
		return false;
	}



}
