<?php

declare(strict_types=1);

namespace Tourze\UserAttributeBundle\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\DependencyInjection\Attribute\When;
use Tourze\UserServiceContracts\UserManagerInterface;

/**
 * 测试用户数据填充
 */
#[When(env: 'test')]
#[When(env: 'dev')]
class TestUserFixtures extends Fixture implements FixtureGroupInterface
{
    public const TEST_USER_REFERENCE = 'test-user-123';

    public function __construct(private readonly UserManagerInterface $userManager)
    {
    }

    public static function getGroups(): array
    {
        return [
            'test-user',
        ];
    }

    public function load(ObjectManager $manager): void
    {
        // 通过 UserManager 创建与测试环境映射一致的用户实体
        $user = $this->userManager->createUser(
            userIdentifier: self::TEST_USER_REFERENCE,
            password: 'password',
            roles: ['ROLE_USER']
        );

        $manager->persist($user);
        $manager->flush();

        // 添加引用以供其他 fixtures 使用
        $this->addReference(self::TEST_USER_REFERENCE, $user);
    }
}
