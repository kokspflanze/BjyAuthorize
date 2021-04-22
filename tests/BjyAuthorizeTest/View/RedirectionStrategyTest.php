<?php

namespace BjyAuthorizeTest\View;

use BjyAuthorize\Guard\Route;
use \PHPUnit\Framework\TestCase;
use BjyAuthorize\View\RedirectionStrategy;
use Laminas\Http\Response;
use Laminas\Mvc\Application;

/**
 * UnauthorizedStrategyTest view strategy test
 *
 * @author Marco Pivetta <ocramius@gmail.com>
 */
class RedirectionStrategyTest extends TestCase
{
    /**
     * @var \BjyAuthorize\View\RedirectionStrategy
     */
    protected $strategy;

    /**
     * {@inheritDoc}
     */
    public function setUp(): void
    {
        parent::setUp();

        $this->strategy = new RedirectionStrategy();
    }

    /**
     * @covers \BjyAuthorize\View\RedirectionStrategy::attach
     * @covers \BjyAuthorize\View\RedirectionStrategy::detach
     */
    public function testAttachDetach()
    {
        $eventManager = $this->getMockBuilder('Laminas\\EventManager\\EventManagerInterface')
            ->getMock();

        $callbackMock = $this->getMockBuilder(\stdClass::class)
            ->setMethods(['__invoke'])
            ->getMock();

        $eventManager
            ->expects($this->once())
            ->method('attach')
            ->with()
            ->will($this->returnValue($callbackMock));
        $this->strategy->attach($eventManager);
        $eventManager
            ->expects($this->once())
            ->method('detach')
            ->with($callbackMock)
            ->will($this->returnValue(true));
        $this->strategy->detach($eventManager);
    }

    /**
     * @covers \BjyAuthorize\View\RedirectionStrategy::onDispatchError
     */
    public function testWillIgnoreUnrecognizedResponse()
    {
        $mvcEvent     = $this->createMock('Laminas\\Mvc\\MvcEvent');
        $response     = $this->createMock('Laminas\\Stdlib\\ResponseInterface');
        $routeMatch   = $this->getMockBuilder('Laminas\\Mvc\\Router\\RouteMatch')->disableOriginalConstructor()->getMock();

        $mvcEvent->expects($this->any())->method('getResponse')->will($this->returnValue($response));
        $mvcEvent->expects($this->any())->method('getRouteMatch')->will($this->returnValue($routeMatch));
        $mvcEvent->expects($this->any())->method('getError')->will($this->returnValue(Route::ERROR));
        $mvcEvent->expects($this->never())->method('setResponse');

        $this->strategy->onDispatchError($mvcEvent);
    }

    /**
     * @covers \BjyAuthorize\View\RedirectionStrategy::onDispatchError
     */
    public function testWillIgnoreUnrecognizedErrorType()
    {
        $mvcEvent     = $this->createMock('Laminas\\Mvc\\MvcEvent');
        $response     = $this->createMock('Laminas\\Http\\Response');
        $routeMatch   = $this->getMockBuilder('Laminas\\Mvc\\Router\\RouteMatch')->disableOriginalConstructor()->getMock();
        $route        = $this->createMock('Laminas\\Router\\RouteInterface');

        $mvcEvent->expects($this->any())->method('getResponse')->will($this->returnValue($response));
        $mvcEvent->expects($this->any())->method('getRouteMatch')->will($this->returnValue($routeMatch));
        $mvcEvent->expects($this->any())->method('getRouter')->will($this->returnValue($route));
        $mvcEvent->expects($this->any())->method('getError')->will($this->returnValue('unknown'));
        $mvcEvent->expects($this->never())->method('setResponse');

        $this->strategy->onDispatchError($mvcEvent);
    }

    /**
     * @covers \BjyAuthorize\View\RedirectionStrategy::onDispatchError
     */
    public function testWillIgnoreOnExistingResult()
    {
        $mvcEvent     = $this->createMock('Laminas\\Mvc\\MvcEvent');
        $response     = $this->createMock('Laminas\\Http\\Response');
        $routeMatch   = $this->getMockBuilder('Laminas\\Mvc\\Router\\RouteMatch')->disableOriginalConstructor()->getMock();

        $mvcEvent->expects($this->any())->method('getResult')->will($this->returnValue($response));
        $mvcEvent->expects($this->any())->method('getResponse')->will($this->returnValue($response));
        $mvcEvent->expects($this->any())->method('getRouteMatch')->will($this->returnValue($routeMatch));
        $mvcEvent->expects($this->any())->method('getError')->will($this->returnValue(Route::ERROR));
        $mvcEvent->expects($this->never())->method('setResponse');

        $this->strategy->onDispatchError($mvcEvent);
    }

    /**
     * @covers \BjyAuthorize\View\RedirectionStrategy::onDispatchError
     */
    public function testWillIgnoreOnMissingRouteMatch()
    {
        $mvcEvent     = $this->createMock('Laminas\\Mvc\\MvcEvent');
        $response     = $this->createMock('Laminas\\Http\\Response');

        $mvcEvent->expects($this->any())->method('getResponse')->will($this->returnValue($response));
        $mvcEvent->expects($this->any())->method('getError')->will($this->returnValue(Route::ERROR));
        $mvcEvent->expects($this->never())->method('setResponse');

        $this->strategy->onDispatchError($mvcEvent);
    }

    /**
     * @covers \BjyAuthorize\View\RedirectionStrategy::onDispatchError
     * @covers \BjyAuthorize\View\RedirectionStrategy::setRedirectRoute
     * @covers \BjyAuthorize\View\RedirectionStrategy::setRedirectUri
     */
    public function testWillRedirectToRouteOnSetRoute()
    {
        $this->strategy->setRedirectRoute('redirect/route');
        $this->strategy->setRedirectUri(null);

        $mvcEvent     = $this->createMock('Laminas\\Mvc\\MvcEvent');
        $response     = $this->createMock('Laminas\\Http\\Response');
        $routeMatch   = $this->getMockBuilder('Laminas\\Router\\RouteMatch')->setMethods([])->disableOriginalConstructor()->getMock();
        $route        = $this->getMockForAbstractClass('Laminas\\Router\\RouteInterface', [], '', true, true, true, ['assemble']);
        $headers      = $this->createMock('Laminas\\Http\\Headers');

        $mvcEvent->expects($this->any())->method('getResponse')->will($this->returnValue($response));
        $mvcEvent->expects($this->any())->method('getRouteMatch')->will($this->returnValue($routeMatch));
        $mvcEvent->expects($this->any())->method('getRouter')->will($this->returnValue($route));
        $mvcEvent->expects($this->any())->method('getError')->will($this->returnValue(Route::ERROR));

        $response->expects($this->any())->method('getHeaders')->will($this->returnValue($headers));
        $response->expects($this->once())->method('setStatusCode')->with(302);

        $headers->expects($this->once())->method('addHeaderLine')->with('Location', 'http://www.example.org/');

        $route
            ->expects($this->any())
            ->method('assemble')
            ->with([], ['name' => 'redirect/route'])
            ->will($this->returnValue('http://www.example.org/'));

        $mvcEvent->expects($this->once())->method('setResponse')->with($response);

        $this->strategy->onDispatchError($mvcEvent);
    }

    /**
     * @covers \BjyAuthorize\View\RedirectionStrategy::onDispatchError
     * @covers \BjyAuthorize\View\RedirectionStrategy::setRedirectUri
     */
    public function testWillRedirectToRouteOnSetUri()
    {
        $this->strategy->setRedirectUri('http://www.example.org/');

        $mvcEvent     = $this->createMock('Laminas\\Mvc\\MvcEvent');
        $response     = $this->createMock('Laminas\\Http\\Response');
        $routeMatch   = $this->getMockBuilder('Laminas\\Mvc\\Router\\RouteMatch')->disableOriginalConstructor()->getMock();
        $route        = $this->createMock('Laminas\\Router\\RouteInterface');
        $headers      = $this->createMock('Laminas\\Http\\Headers');

        $mvcEvent->expects($this->any())->method('getResponse')->will($this->returnValue($response));
        $mvcEvent->expects($this->any())->method('getRouteMatch')->will($this->returnValue($routeMatch));
        $mvcEvent->expects($this->any())->method('getRouter')->will($this->returnValue($route));
        $mvcEvent->expects($this->any())->method('getError')->will($this->returnValue(Route::ERROR));

        $response->expects($this->any())->method('getHeaders')->will($this->returnValue($headers));
        $response->expects($this->once())->method('setStatusCode')->with(302);

        $headers->expects($this->once())->method('addHeaderLine')->with('Location', 'http://www.example.org/');

        $mvcEvent->expects($this->once())->method('setResponse')->with($response);

        $this->strategy->onDispatchError($mvcEvent);
    }

    /**
     * @covers \BjyAuthorize\View\RedirectionStrategy::onDispatchError
     * @covers \BjyAuthorize\View\RedirectionStrategy::setRedirectUri
     */
    public function testWillRedirectToRouteOnSetUriWithApplicationError()
    {
        $this->strategy->setRedirectUri('http://www.example.org/');

        $mvcEvent     = $this->createMock('Laminas\\Mvc\\MvcEvent');
        $response     = $this->createMock('Laminas\\Http\\Response');
        $routeMatch   = $this->getMockBuilder('Laminas\\Mvc\\Router\\RouteMatch')->disableOriginalConstructor()->getMock();
        $route        = $this->createMock('Laminas\\Router\\RouteInterface');
        $headers      = $this->createMock('Laminas\\Http\\Headers');
        $exception    = $this->createMock('BjyAuthorize\\Exception\\UnAuthorizedException');

        $mvcEvent->expects($this->any())->method('getResponse')->will($this->returnValue($response));
        $mvcEvent->expects($this->any())->method('getRouteMatch')->will($this->returnValue($routeMatch));
        $mvcEvent->expects($this->any())->method('getRouter')->will($this->returnValue($route));
        $mvcEvent->expects($this->any())->method('getError')->will($this->returnValue(Application::ERROR_EXCEPTION));
        $mvcEvent->expects($this->any())->method('getParam')->with('exception')->will($this->returnValue($exception));

        $response->expects($this->any())->method('getHeaders')->will($this->returnValue($headers));
        $response->expects($this->once())->method('setStatusCode')->with(302);

        $headers->expects($this->once())->method('addHeaderLine')->with('Location', 'http://www.example.org/');

        $mvcEvent->expects($this->once())->method('setResponse')->with($response);

        $this->strategy->onDispatchError($mvcEvent);
    }
}
