<?php

namespace Keiwen\Cacofony\Controller;

use Keiwen\Cacofony\DependencyInjection\KeiwenCacofonyExtension;
use Keiwen\Cacofony\EntitiesManagement\EntityRegistry;
use Keiwen\Cacofony\Http\Request;
use Keiwen\Cacofony\Http\Response;
use Keiwen\Utils\Object\CacheHandlerTrait;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\DependencyInjection\Exception\ServiceNotFoundException;
use Symfony\Component\HttpFoundation\RedirectResponse;

class DefaultController extends Controller
{
    use CacheHandlerTrait;


    protected $configuration;


    /**
     * @return array
     */
    protected function getConfiguration()
    {
        if(empty($this->configuration)) {
            $this->configuration = $this->getParameter(KeiwenCacofonyExtension::CONTROLLER_CONF);
        }
        return $this->configuration;
    }


    /**
     * @return bool
     */
    protected function prepareCache()
    {
        if($this->cache == null && !$this->cacheDisabled) {
            //load default cache if nothing set
            $config = $this->getConfiguration();
            try {
                $service = $this->get($config['default_cache_service_id']);
                $this->loadCache($service);
            } catch (ServiceNotFoundException $e) {
            }
        }
        return true;
    }


    /**
     * @return bool
     */
    public function hasCacheReadBypass()
    {
        if($this->cacheReadBypass == true) return true;
        $config = $this->getConfiguration();
        if(!empty($config['getparam_disable_cache'])) {
            $request = $this->getRequest();
            if(!empty($request)) {
                $forcedUncache = $request->query->get($config['getparam_disable_cache']);
                if(!empty($forcedUncache)) {
                    $this->getLogger($config['log_channel'])->warning('Force cache bypass');
                    $this->bypassCacheRead();
                    return true;
                }
            }
        }
        return false;
    }


    /**
     * @return Request
     */
    public function getRequest()
    {
        $config = $this->getConfiguration();
        /** @var Request $request */
        $request = $this->get($config['default_request_service_id']);
        return $request;
    }


    /**
     * @return string
     */
    public function retrieveEnvironment()
    {
        /** @var \Symfony\Component\HttpKernel\Kernel $kernel */
        $kernel = $this->get('kernel');
        return $kernel->getEnvironment();
    }


    /**
     * @return EntityRegistry
     */
    protected function getEntityRegistry()
    {
        $config = $this->getConfiguration();
        $serviceName = $config['default_entity_registry_service_id'];
        if(!empty($serviceName)) {
            try {
                /** @var EntityRegistry $service */
                $service = $this->get($serviceName);
                return $service;
            } catch (ServiceNotFoundException $e) {
            }
        }
        throw new \RuntimeException("Entity registry service ('$serviceName') not found");
    }


    /**
     * @param string $objectClass
     * @return \Doctrine\Common\Persistence\ObjectRepository
     */
    public function getRepository(string $objectClass)
    {
        return $this->getEntityRegistry()->getRepository($objectClass);
    }



    /**
     * @param int $status
     * @return RedirectResponse
     */
    protected function redirecToSelf(int $status = Response::HTTP_FOUND)
    {
        return $this->redirect($this->getRequest()->getUrl(true, true), $status);
    }


    /**
     * @param string      $id         The message id (may also be an object that can be cast to string)
     * @param array       $parameters An array of parameters for the message
     * @param string|null $domain     The domain for the message or null to use the default
     * @param string|null $locale     The locale or null to use the default
     *
     * @return string The translated string
     *
     * @throws \InvalidArgumentException If the locale contains invalid characters
     */
    protected function translate(string $id,
                                 array $parameters = array(),
                                 string $domain = null,
                                 string $locale = null)
    {
        /** @var \Symfony\Component\Translation\TranslatorInterface $translator */
        $translator = $this->get('translator');
        return $translator->trans($id, $parameters, $domain, $locale);
    }


    /**
     * @param string $channel
     * @param string $service
     * @return LoggerInterface
     */
    protected function getLogger(string $channel = '', string $service = '')
    {
        $config = $this->getConfiguration();
        $logService = empty($service) ? $config['default_log_service_id'] : $service;
        $toLog = '';
        /** @var LoggerInterface $logger */
        $logger = null;
        if(!empty($channel)) {
            //try getting service log with channel
            try {
                $logger = $this->get($logService . '.' . $channel);
                return $logger;
            } catch (ServiceNotFoundException $e) {
                $toLog = 'Log fallback: cannot find log channel ' . $channel;
            }
        }
        //try getting service log without channel
        try {
            $logger = $this->get($logService);
        } catch (ServiceNotFoundException $e) {
            $toLog = 'Log fallback: cannot find log service ' . $logService;
            $logger = $this->get('logger');
        }
        if(!empty($toLog)) {
            $logger->warning($toLog);
        }
        //return default logger
        return $logger;
    }





}
