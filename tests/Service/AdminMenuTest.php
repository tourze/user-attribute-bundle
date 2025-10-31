<?php

namespace Tourze\UserAttributeBundle\Tests\Service;

use Knp\Menu\MenuFactory;
use Knp\Menu\MenuItem;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use Tourze\EasyAdminMenuBundle\Service\LinkGeneratorInterface;
use Tourze\PHPUnitSymfonyWebTest\AbstractEasyAdminMenuTestCase;
use Tourze\UserAttributeBundle\Service\AdminMenu;

/**
 * @internal
 */
#[CoversClass(AdminMenu::class)]
#[RunTestsInSeparateProcesses]
final class AdminMenuTest extends AbstractEasyAdminMenuTestCase
{
    private AdminMenu $adminMenu;

    private MockLinkGenerator $linkGenerator;

    protected function onSetUp(): void
    {
        $this->linkGenerator = new MockLinkGenerator();
        self::getContainer()->set(LinkGeneratorInterface::class, $this->linkGenerator);
        $this->adminMenu = self::getService(AdminMenu::class);
    }

    /**
     * 测试菜单构建 - 用户模块不存在时
     */
    public function testMenuBuildingWithNewUserModule(): void
    {
        $menuFactory = new MenuFactory();
        $rootMenu = new MenuItem('root', $menuFactory);

        ($this->adminMenu)($rootMenu);

        $userModuleMenu = $rootMenu->getChild('用户模块');
        $this->assertNotNull($userModuleMenu);

        $userAttributeMenu = $userModuleMenu->getChild('用户属性管理');
        $this->assertNotNull($userAttributeMenu);
        $this->assertNotEmpty($userAttributeMenu->getUri());
        $this->assertEquals('fas fa-user-cog', $userAttributeMenu->getAttribute('icon'));
    }

    /**
     * 测试菜单构建 - 用户模块已存在时
     */
    public function testMenuBuildingWithExistingUserModule(): void
    {
        $menuFactory = new MenuFactory();
        $rootMenu = new MenuItem('root', $menuFactory);
        $existingUserMenu = new MenuItem('用户模块', $menuFactory);
        $rootMenu->addChild($existingUserMenu);

        ($this->adminMenu)($rootMenu);

        $userModuleMenu = $rootMenu->getChild('用户模块');
        $this->assertSame($existingUserMenu, $userModuleMenu);

        $userAttributeMenu = $userModuleMenu->getChild('用户属性管理');
        $this->assertNotNull($userAttributeMenu);
        $this->assertNotEmpty($userAttributeMenu->getUri());
        $this->assertEquals('fas fa-user-cog', $userAttributeMenu->getAttribute('icon'));
    }

    /**
     * 测试构造函数和依赖注入
     */
    public function testConstructor(): void
    {
        // 验证 AdminMenu 实例创建成功，依赖注入正常
        $this->assertInstanceOf(AdminMenu::class, $this->adminMenu);
    }
}
