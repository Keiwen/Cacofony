<?php

namespace Keiwen\Cacofony\Controller;

use Keiwen\Cacofony\DependencyInjection\KeiwenCacofonyExtension;
use Keiwen\Cacofony\EntitiesManagement\EntityRegistry;
use Keiwen\Cacofony\EventListener\AutoDumpListener;
use Keiwen\Cacofony\EventListener\TemplateParameterListener;
use Keiwen\Cacofony\Http\Request;
use Keiwen\Cacofony\Http\Response;
use Keiwen\Utils\Object\CacheHandlerTrait;
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
                $service = $this->container->get($config['default_cache']);
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
    protected function getRequest()
    {
        $config = $this->getParameter(KeiwenCacofonyExtension::CONTROLLER_CONF);
        $defaultRequestClass = str_replace('@', '', $config['default_request']);
        /** @var Request $request */
        $request = $this->container->get($defaultRequestClass);
        return $request;
    }

    /**
     * @return Request
     */
    protected function getMainRequest()
    {
        return $this->container->get('request_stack')->getMainRequest();
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
            return $this->container->get($serviceName);
        }
        throw new \RuntimeException("Entity registry service ('$serviceName') not found");
    }


    /**
     * @param string $objectClass
     * @return \Doctrine\Common\Persistence\ObjectRepository
     */
    protected function getRepository(string $objectClass)
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
     * {@inheritdoc}
     * Extended function for autodump
     */
    protected function renderView(string $view, array $parameters = array()): string
    {
        /** @var AutoDumpListener $autodump */
        $autodump = $this->container->get(AutoDumpListener::class);
        $autodump->addParameterToDump($view, $parameters);
        return parent::renderView($view, $parameters);
    }

    public static function getSubscribedServices()
    {
        $subsribedServices = parent::getSubscribedServices();
        $subsribedServices[Request::class] = '?'.Request::class;
        $subsribedServices[AutoDumpListener::class] = '?'.AutoDumpListener::class;
        $subsribedServices[TemplateParameterListener::class] = '?'.TemplateParameterListener::class;
        $subsribedServices[EntityRegistry::class] = '?'.EntityRegistry::class;
        return $subsribedServices;
    }


}
