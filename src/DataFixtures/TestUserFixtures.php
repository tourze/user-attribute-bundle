<?php

declare(strict_types=1);

namespace Tourze\UserAttributeBundle\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\DependencyInjection\Attribute\When;
use Tourze\UserAttributeBundle\Entity\TestUser;

/**
 * 测试用户数据填充
 */
#[When(env: 'test')]
#[When(env: 'dev')]
class TestUserFixtures extends Fixture implements FixtureGroupInterface
{
    public const TEST_USER_REFERENCE = 'test-user-123';

    public static function getGroups(): array
    {
        return [
            'test-user',
        ];
    }

    public function load(ObjectManager $manager): void
    {
        // 创建测试用户
        $testUser = new TestUser();
        $testUser->setIdentifier('test-user-123');
        $manager->persist($testUser);

        // 添加引用以供其他 fixtures 使用
        $this->addReference(self::TEST_USER_REFERENCE, $testUser);

        $manager->flush();
    }
}
