<?php

namespace Keiwen\Cacofony\EventListener;


use Keiwen\Cacofony\Configuration\RestrictToRole;
use Keiwen\Cacofony\Security\RoleChecker;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Component\HttpKernel\Event\ControllerEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

class RoleCheckerListener
{

    protected $roleChecker;

    public function __construct(RoleChecker $roleChecker)
    {
        $this->roleChecker = $roleChecker;
    }

    /**
     * Called just before each controller.
     * Check role attribution
     * @param ControllerEvent $event
     */
    #[AsEventListener(event: KernelEvents::CONTROLLER, priority: 50)]
    public function onKernelController(ControllerEvent $event)
    {
        $controller = $event->getController();
        if(!is_array($controller)) return;
        [$object, $method] = $controller;

        $restrictions = array();

        // use reflection to get restrict to role attributes
        try {
            // ON CONTROLLER CLASS ITSELF
            $reflectionClass = new \ReflectionClass($object);
            $reflectionAttributes = $reflectionClass->getAttributes(RestrictToRole::class);
            foreach ($reflectionAttributes as $reflectionAttribute) {
                $restrictToRole = $reflectionAttribute->newInstance();
                $restrictions[] = $restrictToRole;
            }

            // ON CONTROLLER ACTION
            $reflectionMethod = new \ReflectionMethod($object, $method);
            $reflectionAttributes = $reflectionMethod->getAttributes(RestrictToRole::class);
            foreach ($reflectionAttributes as $reflectionAttribute) {
                $restrictToRole = $reflectionAttribute->newInstance();
                $restrictions[] = $restrictToRole;
            }

        } catch (\Exception $e) {
            // do nothing
        }

        foreach ($restrictions as $restriction) {
            if (!$this->roleChecker->hasRoleFromRestrictionAttribute($restriction)) {
                throw new AccessDeniedException();
            }
        }

    }


}
