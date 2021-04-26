<?php

namespace BjyAuthorizeTest\Provider\Resource;

use \PHPUnit\Framework\TestCase;
use BjyAuthorize\Provider\Resource\Config;

/**
 * Config resource provider test
 *
 * @author Marco Pivetta <ocramius@gmail.com>
 */
class ConfigTest extends TestCase
{
    /**
     * @covers \BjyAuthorize\Provider\Resource\Config::__construct
     * @covers \BjyAuthorize\Provider\Resource\Config::getResources
     */
    public function testGetResources()
    {
        $config = new Config(['resource1', 'resource2',]);

        $resources = $config->getResources();

        $this->assertCount(2, $resources);
        $this->assertContains('resource1', $resources);
        $this->assertContains('resource2', $resources);
    }
}
