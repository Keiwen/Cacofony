<?php

namespace Keiwen\Cacofony\Controller;

use Keiwen\Cacofony\DependencyInjection\KeiwenCacofonyExtension;
use Keiwen\Cacofony\EntitiesManagement\EntityRegistry;
use Keiwen\Cacofony\EventListener\AutoDumpListener;
use Keiwen\Cacofony\EventListener\ParamFetcherListener;
use Keiwen\Cacofony\EventListener\TemplateParameterListener;
use Keiwen\Cacofony\Http\Request;
use Keiwen\Cacofony\Http\Response;
use Keiwen\Utils\Object\CacheHandlerTrait;
use Psr\Log\LoggerInterface;
use Symfony\Component\DependencyInjection\Exception\ServiceNotFoundException;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class DefaultController extends AbstractController
{
    use CacheHandlerTrait;


    /**
     * @return bool
     */
    protected function prepareCache()
    {
        if($this->hasCacheLoaded()) {
            //load default cache if nothing set
            $config = $this->getParameter(KeiwenCacofonyExtension::CONTROLLER_CONF);
            try {
                $service = $this->get($config['default_cache']);
                $this->loadCache($service);
            } catch (ServiceNotFoundException $e) {
                return false;
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
        $config = $this->getParameter(KeiwenCacofonyExtension::CONTROLLER_CONF);
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
        $config = $this->getParameter(KeiwenCacofonyExtension::CONTROLLER_CONF);
        $defaultRequestClass = str_replace('@', '', $config['default_request']);
        /** @var Request $request */
        $request = $this->get($defaultRequestClass);
        return $request;
    }

    /**
     * @return Request
     */
    protected function getMasterRequest()
    {
        return $this->get('request_stack')->getMasterRequest();
    }


    /**
     * @return EntityRegistry
     * @throws ServiceNotFoundException, RuntimeException
     */
    protected function getEntityRegistry()
    {
        $config = $this->getParameter(KeiwenCacofonyExtension::CONTROLLER_CONF);
        $serviceName = $config['default_entity_registry'];
        if(!empty($serviceName)) {
            /** @var EntityRegistry $service */
            return $this->get($serviceName);
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
    protected function redirectToSelf(int $status = Response::HTTP_FOUND)
    {
        return $this->redirect($this->getRequest()->getUrl(true, true), $status);
    }


    /**
     * @param string $channel
     * @param string $service
     * @return LoggerInterface
     */
    protected function getLogger(string $channel = '', string $service = '')
    {
        $config = $this->getParameter(KeiwenCacofonyExtension::CONTROLLER_CONF);
        $logService = empty($service) ? $config['default_log'] : $service;
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
        //return logger
        return $logger;
    }


    /**
     * {@inheritdoc}
     * Extended function for autodump
     */
    protected function renderView(string $view, array $parameters = array()): string
    {
        /** @var AutoDumpListener $autodump */
        $autodump = $this->get(AutoDumpListener::class);
        $autodump->addParameterToDump($view, $parameters);
        return parent::renderView($view, $parameters);
    }

    public static function getSubscribedServices()
    {
        $subsribedServices = parent::getSubscribedServices();
        $subsribedServices[Request::class] = '?'.Request::class;
        $subsribedServices[AutoDumpListener::class] = '?'.AutoDumpListener::class;
        $subsribedServices[ParamFetcherListener::class] = '?'.ParamFetcherListener::class;
        $subsribedServices[TemplateParameterListener::class] = '?'.TemplateParameterListener::class;
        return $subsribedServices;
    }


}
