<?php

declare(strict_types=1);

namespace Tourze\UserAttributeBundle\DataFixtures;

use Carbon\CarbonImmutable;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\DependencyInjection\Attribute\When;
use Tourze\UserAttributeBundle\Entity\TestUser;
use Tourze\UserAttributeBundle\Entity\UserAttribute;

/**
 * 用户属性数据填充
 *
 * 为系统用户创建各种属性配置，用于演示用户扩展属性功能
 */
#[When(env: 'test')]
#[When(env: 'dev')]
class UserAttributeFixtures extends Fixture implements FixtureGroupInterface
{
    // 用户属性引用常量
    public const ADMIN_AVATAR_ATTRIBUTE_REFERENCE = 'admin-avatar-attribute';
    public const ADMIN_PROFILE_ATTRIBUTE_REFERENCE = 'admin-profile-attribute';
    public const USER_PREFERENCE_ATTRIBUTE_REFERENCE = 'user-preference-attribute';
    public const USER_SETTING_ATTRIBUTE_REFERENCE = 'user-setting-attribute';

    // 属性名称常量
    // private const ATTR_AVATAR_URL = 'avatar_url';
    // private const ATTR_PROFILE_SUMMARY = 'profile_summary';
    // private const ATTR_THEME_PREFERENCE = 'theme_preference';
    // private const ATTR_NOTIFICATION_SETTING = 'notification_setting';
    // private const ATTR_LAST_LOGIN_DEVICE = 'last_login_device';
    // private const ATTR_API_TOKEN = 'api_token';

    public static function getGroups(): array
    {
        return [
            'user-attribute',
        ];
    }

    public function load(ObjectManager $manager): void
    {
        // 获取测试用户
        $user = $this->getReference(TestUserFixtures::TEST_USER_REFERENCE, TestUser::class);

        // 创建一些用户属性
        $attributes = [
            [
                'name' => 'theme_preference',
                'value' => '{"theme": "dark", "language": "zh_CN"}',
                'remark' => '用户主题偏好',
            ],
            [
                'name' => 'notification_settings',
                'value' => '{"email": true, "sms": false}',
                'remark' => '通知设置',
            ],
            [
                'name' => 'last_login_device',
                'value' => 'Chrome/Windows',
                'remark' => '最后登录设备',
            ],
            [
                'name' => 'avatar_url',
                'value' => 'https://cdn.jsdelivr.net/gh/twitter/twemoji@14.0.2/assets/svg/1f600.svg',
                'remark' => '用户头像',
            ],
            [
                'name' => 'profile_summary',
                'value' => '这是一个测试用户的简介信息',
                'remark' => '个人简介',
            ],
        ];

        foreach ($attributes as $i => $attrData) {
            $attribute = new UserAttribute();
            $attribute->setUser($user);
            $attribute->setName($attrData['name']);
            $attribute->setValue($attrData['value']);
            $attribute->setRemark($attrData['remark']);
            $attribute->setCreateTime(CarbonImmutable::now()->modify('-' . rand(1, 30) . ' days'));
            $attribute->setUpdateTime(CarbonImmutable::now()->modify('-' . rand(1, 7) . ' days'));

            $manager->persist($attribute);

            // 添加引用以供其他 fixtures 使用
            if (0 === $i) {
                $this->addReference(self::USER_PREFERENCE_ATTRIBUTE_REFERENCE, $attribute);
            }
        }

        $manager->flush();
    }
}
