<?php

/**
 * BjyAuthorize Module (https://github.com/bjyoungblood/BjyAuthorize)
 *
 * @link https://github.com/bjyoungblood/BjyAuthorize for the canonical source repository
 * @license http://framework.zend.com/license/new-bsd New BSD License
 */

namespace BjyAuthorizeTest\Guard;

use BjyAuthorize\Exception\UnAuthorizedException;
use BjyAuthorize\Guard\Route;
use BjyAuthorize\Service\Authorize;
use Laminas\Console\Request as ConsoleRequest;
use Laminas\EventManager\EventManagerInterface;
use Laminas\EventManager\ResponseCollection;
use Laminas\Http\Request as HttpRequest;
use Laminas\Mvc\Application;
use Laminas\Mvc\MvcEvent;
use Laminas\Router\RouteMatch;
use Laminas\ServiceManager\ServiceLocatorInterface;
use PHPUnit\Framework\TestCase;

/**
 * Route Guard test
 *
 * @author Marco Pivetta <ocramius@gmail.com>
 */
class RouteTest extends TestCase
{
    /**
     * @var \Laminas\ServiceManager\ServiceLocatorInterface|\PHPUnit\Framework\MockObject\MockObject
     */
    protected $serviceLocator;

    /**
     * @var \BjyAuthorize\Service\Authorize|\PHPUnit\Framework\MockObject\MockObject
     */
    protected $authorize;

    /**
     * @var Route
     */
    protected $routeGuard;

    /**
     * {@inheritDoc}
     *
     * @covers \BjyAuthorize\Guard\Route::__construct
     */
    public function setUp(): void
    {
        parent::setUp();

        $this->serviceLocator = $this->getMockBuilder(ServiceLocatorInterface::class)
            ->getMock();
        $this->authorize = $authorize = $this->getMockBuilder(Authorize::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->routeGuard = new Route([], $this->serviceLocator);

        $this
            ->serviceLocator
            ->expects($this->any())
            ->method('get')
            ->with(Authorize::class)
            ->will($this->returnValue($authorize));
    }

    /**
     * @covers \BjyAuthorize\Guard\Route::attach
     * @covers \BjyAuthorize\Guard\Route::detach
     */
    public function testAttachDetach()
    {
        $eventManager = $this->createMock(EventManagerInterface::class);

        $callbackDummy = new class {
            public function __invoke()
            {
            }
        };

        $eventManager
            ->expects($this->once())
            ->method('attach')
            ->with()
            ->will($this->returnValue($callbackDummy));
        $this->routeGuard->attach($eventManager);
        $eventManager
            ->expects($this->once())
            ->method('detach')
            ->with($callbackDummy)
            ->will($this->returnValue(true));
        $this->routeGuard->detach($eventManager);
    }

    /**
     * @covers \BjyAuthorize\Guard\Route::__construct
     * @covers \BjyAuthorize\Guard\Route::getResources
     * @covers \BjyAuthorize\Guard\Route::getRules
     */
    public function testGetResourcesGetRules()
    {
        $controller = new Route(
            [
                 [
                     'route' => 'test/route',
                     'roles' => [
                         'admin',
                         'user',
                     ],
                 ],
                 [
                     'route' => 'test2-route',
                     'roles' => [
                         'admin2',
                         'user2',
                     ],
                 ],
                 [
                     'route' => 'test3-route',
                     'roles' => 'admin3'
                 ],
            ],
            $this->serviceLocator
        );

        $resources = $controller->getResources();

        $this->assertCount(3, $resources);
        $this->assertContains('route/test/route', $resources);
        $this->assertContains('route/test2-route', $resources);
        $this->assertContains('route/test3-route', $resources);

        $rules = $controller->getRules();

        $this->assertCount(3, $rules['allow']);
        $this->assertContains(
            [['admin', 'user'], 'route/test/route'],
            $rules['allow']
        );
        $this->assertContains(
            [['admin2', 'user2'], 'route/test2-route'],
            $rules['allow']
        );
        $this->assertContains(
            [['admin3'], 'route/test3-route'],
            $rules['allow']
        );
    }

    /**
     * @covers \BjyAuthorize\Guard\Route::__construct
     * @covers \BjyAuthorize\Guard\Route::getRules
     */
    public function testGetRulesWithAssertion()
    {
        $controller = new Route(
            [
                 [
                     'route' => 'test/route',
                     'roles' => [
                         'admin',
                         'user',
                     ],
                     'assertion' => 'test-assertion'
                 ],
            ],
            $this->serviceLocator
        );

        $rules = $controller->getRules();

        $this->assertCount(1, $rules['allow']);
        $this->assertContains(
            [['admin', 'user'], 'route/test/route', null, 'test-assertion'],
            $rules['allow']
        );
    }

    /**
     * @covers \BjyAuthorize\Guard\Route::onRoute
     */
    public function testOnRouteWithValidRoute()
    {
        $event = $this->createMvcEvent('test-route');
        $event->getTarget()->getEventManager()->expects($this->never())->method('triggerEvent');
        $this
            ->authorize
            ->expects($this->any())
            ->method('isAllowed')
            ->will(
                $this->returnValue(
                    function ($resource) {
                        return $resource === 'route/test-route';
                    }
                )
            );

        $this->assertNull($this->routeGuard->onRoute($event), 'Does not stop event propagation');
    }

    /**
     * @covers \BjyAuthorize\Guard\Route::onRoute
     */
    public function testOnRouteWithInvalidResource()
    {
        $event = $this->createMvcEvent('test-route');
        $this->authorize->expects($this->any())->method('getIdentity')->will($this->returnValue('admin'));
        $this
            ->authorize
            ->expects($this->any())
            ->method('isAllowed')
            ->will($this->returnValue(false));
        $event->expects($this->once())->method('setError')->with(Route::ERROR);

        $event->expects($this->exactly(3))->method('setParam')->withConsecutive(
            ['route', 'test-route'],
            ['identity', 'admin'],
            ['exception', $this->isInstanceOf(UnAuthorizedException::class)]
        );

        $responseCollection = $this->getMockBuilder(ResponseCollection::class)
            ->getMock();

        $event->setName(MvcEvent::EVENT_DISPATCH_ERROR);
        $event
            ->getTarget()
            ->getEventManager()
            ->expects($this->once())
            ->method('triggerEvent')
            ->with($event)
            ->willReturn($responseCollection);

        $this->assertNull($this->routeGuard->onRoute($event), 'Does not stop event propagation');
    }

    /**
     * @covers \BjyAuthorize\Guard\Controller::onDispatch
     */
    public function testOnDispatchWithInvalidResourceConsole()
    {
        $event = $this->getMockBuilder(MvcEvent::class)
            ->getMock();
        $routeMatch = $this->getMockBuilder(RouteMatch::class)
            ->disableOriginalConstructor()
            ->getMock();
        $request = $this->getMockBuilder(ConsoleRequest::class)
            ->disableOriginalConstructor()
            ->getMock();
        $event->method('getRouteMatch')->willReturn($routeMatch);
        $event->method('getRequest')->willReturn($request);

        $this->assertNull($this->routeGuard->onRoute($event), 'Does not stop event propagation');
    }

    /**
     * @param string|null $route
     *
     * @return \PHPUnit\Framework\MockObject\MockObject|\Laminas\Mvc\MvcEvent
     */
    private function createMvcEvent($route = null)
    {
        $eventManager = $this->getMockBuilder(EventManagerInterface::class)
            ->getMock();
        $application = $this->getMockBuilder(Application::class)
            ->disableOriginalConstructor()
            ->getMock();
        $event = $this->getMockBuilder(MvcEvent::class)
            ->getMock();
        $routeMatch = $this->getMockBuilder(RouteMatch::class)
            ->disableOriginalConstructor()
            ->getMock();
        $request = $this->getMockBuilder(HttpRequest::class)
            ->getMock();

        $event->expects($this->any())->method('getRouteMatch')->will($this->returnValue($routeMatch));
        $event->expects($this->any())->method('getRequest')->will($this->returnValue($request));
        $event->expects($this->any())->method('getTarget')->will($this->returnValue($application));
        $application->expects($this->any())->method('getEventManager')->will($this->returnValue($eventManager));
        $routeMatch->expects($this->any())->method('getMatchedRouteName')->will($this->returnValue($route));

        return $event;
    }
}
