<?php
/**
 * BjyAuthorize Module (https://github.com/bjyoungblood/BjyAuthorize)
 *
 * @link https://github.com/bjyoungblood/BjyAuthorize for the canonical source repository
 * @license http://framework.zend.com/license/new-bsd New BSD License
 */

namespace BjyAuthorizeTest\View\Helper;

use \PHPUnit\Framework\TestCase;
use BjyAuthorize\View\Helper\IsAllowed;

/**
 * IsAllowed view helper test
 *
 * @author Marco Pivetta <ocramius@gmail.com>
 */
class IsAllowedTest extends TestCase
{
    /**
     * @covers \BjyAuthorize\View\Helper\IsAllowed
     */
    public function testIsAllowed()
    {
        $authorize = $this->getMockBuilder('BjyAuthorize\\Service\\Authorize')->disableOriginalConstructor()->getMock();
        $authorize
            ->expects($this->once())
            ->method('isAllowed')
            ->with('test', 'privilege')
            ->will($this->returnValue(true));

        $plugin = new IsAllowed($authorize);
        $this->assertTrue($plugin->__invoke('test', 'privilege'));

        $authorize2 = $this->getMockBuilder('BjyAuthorize\\Service\\Authorize')->disableOriginalConstructor()->getMock();
        $authorize2
            ->expects($this->once())
            ->method('isAllowed')
            ->with('test2', 'privilege2')
            ->will($this->returnValue(false));

        $plugin = new IsAllowed($authorize2);

        $this->assertFalse($plugin->__invoke('test2', 'privilege2'));
    }
}
