<?php

namespace Keiwen\Cacofony\Controller;

use Keiwen\Cacofony\DependencyInjection\KeiwenCacofonyExtension;
use Keiwen\Cacofony\EntitiesManagement\EntityRegistry;
use Keiwen\Cacofony\FormProcessor\DefaultFormProcessor;
use Keiwen\Cacofony\Http\Request;
use Doctrine\Common\Cache\Cache;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\DependencyInjection\Exception\ServiceNotFoundException;
use Symfony\Component\HttpFoundation\RedirectResponse;

class DefaultController extends Controller
{
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
    public function isCacheUsed()
    {
        $config = $this->getConfiguration();
        if(!empty($config['getparam_disable_cache'])) {
            $request = $this->getRequest();
            if(!empty($request)) {
                $forcedUncache = $request->query->get($config['getparam_disable_cache']);
                if(!empty($forcedUncache)) {
                    $this->getLogger($config['log_channel'])->warning('Force cache bypass');
                    return false;
                }
            }
        }
        return !empty($config['default_cache_service_id']);
    }


    /**
     * @param string $serviceName
     * @return Cache
     */
    public function getCache(string $serviceName = '') {
        $config = $this->getConfiguration();
        $serviceName = empty($serviceName) ? $config['default_cache_service_id'] : $serviceName;
        try {
            /** @var Cache $service */
            $service = $this->get($serviceName);
            return $service;
        } catch (ServiceNotFoundException $e) {
            return null;
        }
    }


    /**
     * @param string $key
     * @param string $serviceName
     * @return mixed|null
     */
    public function readInCache(string $key, string $serviceName = '')
    {
        if(!$this->isCacheUsed()) return null;
        $cache = $this->getCache($serviceName);
        if(!$cache) return null;
        try {
            if($cache->contains($key)) {
                return $cache->fetch($key);
            }
        } catch (\Exception $e) {
        }
        return null;
    }

    /**
     * @param string $key
     * @param string $serviceName
     * @return bool
     */
    public function isInCache(string $key, string $serviceName = '')
    {
        if(!$this->isCacheUsed()) return false;
        $cache = $this->getCache($serviceName);
        if(!$cache) return false;
        try {
            return $cache->contains($key);
        } catch (\Exception $e) {
        }
        return false;
    }


    /**
     * @param string $key
     * @param mixed $data
     * @param int $cacheLifetime
     * @param string $serviceName
     * @return bool
     */
    public function storeInCache(string $key, $data, int $cacheLifetime = 0, string $serviceName = '')
    {
        $cache = $this->getCache($serviceName);
        if(!$cache) return false;
        try {
            return $cache->save($key, $data, $cacheLifetime);
        } catch (\Exception $e) {
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
    protected function redirecToSelf(int $status = 302)
    {
        return $this->redirect($this->getRequest()->getUrl(true, true), $status);
    }


    /**
     * @param DefaultFormProcessor $processor
     * @return \Symfony\Component\Form\Form
     */
    public function createFormFromProcessor(DefaultFormProcessor $processor)
    {
        return $this->container->get('form.factory')->create($processor->getFormClass(), $processor->getDefaultData(), $processor->getFormOptions());
    }


    public function createEntityForm(string $formClass, $entity, array $options = array())
    {
        return $this->createForm($formClass, $entity, $options);
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
        /** @var \Symfony\Component\Translation\Translator $translator */
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
