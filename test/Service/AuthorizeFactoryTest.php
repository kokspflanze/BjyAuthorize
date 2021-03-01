<?php
/**
 * BjyAuthorize Module (https://github.com/bjyoungblood/BjyAuthorize)
 *
 * @link https://github.com/bjyoungblood/BjyAuthorize for the canonical source repository
 * @license http://framework.zend.com/license/new-bsd New BSD License
 */

namespace BjyAuthorizeTest\Service;

use BjyAuthorize\Service\Authorize;
use BjyAuthorize\Service\AuthorizeFactory;
use Laminas\ServiceManager\ServiceManager;
use PHPUnit\Framework\TestCase;

/**
 * Test for {@see \BjyAuthorize\Service\AuthorizeFactory}
 *
 * @author Christian Bergau <cbergau86@gmail.com>
 */
class AuthorizeFactoryTest extends TestCase
{
    /**
     * @covers \BjyAuthorize\Service\AuthorizeFactory::__invoke
     */
    public function testInvokeSetCacheOptionsIfCacheIsEnabledAndAdapterOptionsAreProvided()
    {
        $serviceManager = new ServiceManager();
        $serviceManager->setService('BjyAuthorize\Config', ['cache_key' => 'bjyauthorize_acl']);

        $authorizeFactory = new AuthorizeFactory();

        $authorize = $authorizeFactory($serviceManager, AuthorizeFactory::class);

        $this->assertInstanceOf(Authorize::class, $authorize);
    }
}
