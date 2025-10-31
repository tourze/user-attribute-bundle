<?php

namespace Tourze\UserAttributeBundle\Tests\DependencyInjection;

use PHPUnit\Framework\Attributes\CoversClass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Tourze\PHPUnitSymfonyUnitTest\AbstractDependencyInjectionExtensionTestCase;
use Tourze\UserAttributeBundle\DependencyInjection\UserAttributeExtension;

/**
 * @internal
 */
#[CoversClass(UserAttributeExtension::class)]
final class UserAttributeExtensionTest extends AbstractDependencyInjectionExtensionTestCase
{
    private UserAttributeExtension $extension;

    private ContainerBuilder $container;

    protected function setUp(): void
    {
        parent::setUp();

        $this->extension = new UserAttributeExtension();
        $this->container = new ContainerBuilder();
        $this->container->setParameter('kernel.environment', 'test');
    }

    /**
     * 测试扩展加载
     */
    public function testLoad(): void
    {
        $configs = [];
        $this->extension->load($configs, $this->container);

        // 验证容器配置已正确处理
        $this->assertNotEmpty($this->container->getDefinitions());
    }

    /**
     * 测试扩展别名
     */
    public function testGetAlias(): void
    {
        $this->assertEquals('user_attribute', $this->extension->getAlias());
    }
}
