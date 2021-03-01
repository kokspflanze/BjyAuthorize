<?php
/**
 * BjyAuthorize Module (https://github.com/bjyoungblood/BjyAuthorize)
 *
 * @link https://github.com/bjyoungblood/BjyAuthorize for the canonical source repository
 * @license http://framework.zend.com/license/new-bsd New BSD License
 */

namespace BjyAuthorize\Guard;

use BjyAuthorize\Exception\UnAuthorizedException;
use BjyAuthorize\Service\Authorize;
use Laminas\Console\Request as ConsoleRequest;
use Laminas\EventManager\EventManagerInterface;
use Laminas\Mvc\MvcEvent;

/**
 * Route Guard listener, allows checking of permissions
 * during {@see \Laminas\Mvc\MvcEvent::EVENT_ROUTE}
 *
 * @author Ben Youngblood <bx.youngblood@gmail.com>
 */
class Route extends AbstractGuard
{
    /**
     * Marker for invalid route errors
     */
    const ERROR = 'error-unauthorized-route';

    protected function extractResourcesFromRule(array $rule)
    {
        return ['route/' . $rule['route']];
    }

    /**
     * {@inheritDoc}
     */
    public function attach(EventManagerInterface $events, $priority = 1)
    {
        $this->listeners[] = $events->attach(MvcEvent::EVENT_ROUTE, [$this, 'onRoute'], -1000);
    }

    /**
     * Event callback to be triggered on dispatch, causes application error triggering
     * in case of failed authorization check
     *
     * @param MvcEvent $event
     *
     * @return mixed
     */
    public function onRoute(MvcEvent $event)
    {
        /* @var $service \BjyAuthorize\Service\Authorize */
        $service = $this->container->get(Authorize::class);
        $match = $event->getRouteMatch();
        $routeName = $match->getMatchedRouteName();

        if ($service->isAllowed('route/' . $routeName) || (class_exists(ConsoleRequest::class) && $event->getRequest() instanceof ConsoleRequest)) {
            return;
        }

        $event->setError(static::ERROR);
        $event->setParam('route', $routeName);
        $event->setParam('identity', $service->getIdentity());
        $event->setParam('exception', new UnAuthorizedException('You are not authorized to access ' . $routeName));

        /* @var $app \Laminas\Mvc\Application */
        $app = $event->getTarget();
        $eventManager = $app->getEventManager();

        $event->setName(MvcEvent::EVENT_DISPATCH_ERROR);
        $results = $eventManager->triggerEvent($event);

        $return  = $results->last();
        if (! $return) {
            return $event->getResult();
        }

        return $return;
    }
}
