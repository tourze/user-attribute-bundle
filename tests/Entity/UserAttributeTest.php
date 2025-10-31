<?php

namespace Tourze\UserAttributeBundle\Tests\Entity;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Security\Core\User\UserInterface;
use Tourze\PHPUnitDoctrineEntity\AbstractEntityTestCase;
use Tourze\UserAttributeBundle\Entity\UserAttribute;

/**
 * @internal
 */
#[CoversClass(UserAttribute::class)]
final class UserAttributeTest extends AbstractEntityTestCase
{
    protected function createEntity(): object
    {
        return new UserAttribute();
    }

    /** @return iterable<string, array{string, mixed}> */
    public static function propertiesProvider(): iterable
    {
        return [
            'name' => ['name', 'test_value'],
        ];
    }

    private UserAttribute $attribute;

    protected function setUp(): void
    {
        parent::setUp();

        $this->attribute = new UserAttribute();
    }

    /**
     * 测试ID获取
     */
    public function testGetId(): void
    {
        // 由于ID是由Doctrine生成的，新创建的实体ID应该是null
        $this->assertNull($this->attribute->getId());
    }

    /**
     * 测试用户关联设置
     */
    public function testSetUser(): void
    {
        $user = new class implements UserInterface {
            public function getRoles(): array
            {
                return ['ROLE_USER'];
            }

            public function getPassword(): null
            {
                return null;
            }

            public function getSalt(): null
            {
                return null;
            }

            public function getUsername(): string
            {
                return 'test_user';
            }

            public function getUserIdentifier(): string
            {
                return 'test_user';
            }

            public function eraseCredentials(): void
            {
            }
        };

        $this->attribute->setUser($user);

        // 检查用户是否正确设置
        $this->assertSame($user, $this->attribute->getUser());
    }

    /**
     * 测试用户关联设置为null
     */
    public function testSetUserWithNull(): void
    {
        $this->attribute->setUser(null);

        // 检查用户是否正确设置为null
        $this->assertNull($this->attribute->getUser());
    }

    /**
     * 测试属性名设置
     */
    public function testSetName(): void
    {
        $this->attribute->setName('new_attribute');

        // 检查属性名是否正确设置
        $this->assertEquals('new_attribute', $this->attribute->getName());
    }

    /**
     * 测试属性值设置
     */
    public function testSetValue(): void
    {
        $this->attribute->setValue('new_value');

        // 检查属性值是否正确设置
        $this->assertEquals('new_value', $this->attribute->getValue());
    }

    /**
     * 测试备注设置
     */
    public function testSetRemark(): void
    {
        $this->attribute->setRemark('新的备注');

        // 检查备注是否正确设置
        $this->assertEquals('新的备注', $this->attribute->getRemark());
    }

    /**
     * 测试备注设置为null
     */
    public function testSetRemarkWithNull(): void
    {
        $this->attribute->setRemark(null);

        // 检查备注是否正确设置为null
        $this->assertNull($this->attribute->getRemark());
    }

    /**
     * 测试创建人设置
     */
    public function testSetCreatedBy(): void
    {
        $this->attribute->setCreatedBy('admin_user');

        // 检查创建人是否正确设置
        $this->assertEquals('admin_user', $this->attribute->getCreatedBy());
    }

    /**
     * 测试更新人设置
     */
    public function testSetUpdatedBy(): void
    {
        $this->attribute->setUpdatedBy('moderator_user');

        // 检查更新人是否正确设置
        $this->assertEquals('moderator_user', $this->attribute->getUpdatedBy());
    }

    /**
     * 测试创建IP设置
     */
    public function testSetCreatedFromIp(): void
    {
        $this->attribute->setCreatedFromIp('10.0.0.1');

        // 检查创建IP是否正确设置
        $this->assertEquals('10.0.0.1', $this->attribute->getCreatedFromIp());
    }

    /**
     * 测试更新IP设置
     */
    public function testSetUpdatedFromIp(): void
    {
        $this->attribute->setUpdatedFromIp('10.0.0.2');

        // 检查更新IP是否正确设置
        $this->assertEquals('10.0.0.2', $this->attribute->getUpdatedFromIp());
    }

    /**
     * 测试API数组表示
     */
    public function testRetrieveApiArray(): void
    {
        // 设置ID（模拟数据库生成的ID）
        $reflection = new \ReflectionClass($this->attribute);
        $idProperty = $reflection->getProperty('id');
        $idProperty->setAccessible(true);
        $idProperty->setValue($this->attribute, '123456789');

        $this->attribute->setName('user_preference');
        $this->attribute->setValue('dark_theme');

        $result = $this->attribute->retrieveApiArray();
        $this->assertArrayHasKey('id', $result);
        $this->assertArrayHasKey('name', $result);
        $this->assertArrayHasKey('value', $result);

        $this->assertEquals('123456789', $result['id']);
        $this->assertEquals('user_preference', $result['name']);
        $this->assertEquals('dark_theme', $result['value']);

        // 确保只包含API需要的字段
        $this->assertCount(3, $result);
    }

    /**
     * 测试管理员数组表示
     */
    public function testRetrieveAdminArray(): void
    {
        // 设置ID（模拟数据库生成的ID）
        $reflection = new \ReflectionClass($this->attribute);
        $idProperty = $reflection->getProperty('id');
        $idProperty->setAccessible(true);
        $idProperty->setValue($this->attribute, '123456789');

        $this->attribute->setName('user_preference');
        $this->attribute->setValue('dark_theme');
        $this->attribute->setRemark('用户主题偏好设置');

        $result = $this->attribute->retrieveAdminArray();
        $this->assertArrayHasKey('id', $result);
        $this->assertArrayHasKey('name', $result);
        $this->assertArrayHasKey('value', $result);
        $this->assertArrayHasKey('remark', $result);

        $this->assertEquals('123456789', $result['id']);
        $this->assertEquals('user_preference', $result['name']);
        $this->assertEquals('dark_theme', $result['value']);
        $this->assertEquals('用户主题偏好设置', $result['remark']);

        // 确保包含管理员需要的所有字段
        $this->assertCount(4, $result);
    }

    /**
     * 测试属性值为null的情况
     */
    public function testGetValueWithNullValue(): void
    {
        // 默认值应该是null
        $this->assertNull($this->attribute->getValue());

        // 由于setValue方法要求string类型，我们测试空字符串的情况
        $this->attribute->setValue('');
        $this->assertEquals('', $this->attribute->getValue());
    }

    /**
     * 测试空字符串属性值
     */
    public function testSetValueWithEmptyString(): void
    {
        $this->attribute->setValue('');
        $this->assertEquals('', $this->attribute->getValue());
    }

    /**
     * 测试长属性值
     */
    public function testSetValueWithLongString(): void
    {
        $longValue = str_repeat('这是一个很长的属性值', 100);
        $this->attribute->setValue($longValue);
        $this->assertEquals($longValue, $this->attribute->getValue());
    }

    /**
     * 测试特殊字符属性值
     */
    public function testSetValueWithSpecialCharacters(): void
    {
        $specialValue = 'test@#$%^&*()_+-={}[]|\:";\'<>?,./`~';
        $this->attribute->setValue($specialValue);
        $this->assertEquals($specialValue, $this->attribute->getValue());
    }

    /**
     * 测试JSON格式属性值
     */
    public function testSetValueWithJsonString(): void
    {
        $jsonValue = '{"key": "value", "number": 123, "array": [1, 2, 3]}';
        $this->attribute->setValue($jsonValue);
        $this->assertEquals($jsonValue, $this->attribute->getValue());
    }

    /**
     * 测试时间设置
     */
    public function testTimeSetters(): void
    {
        $createTime = new \DateTimeImmutable('2023-01-01 10:00:00');
        $updateTime = new \DateTimeImmutable('2023-01-02 15:30:00');

        $this->attribute->setCreateTime($createTime);
        $this->attribute->setUpdateTime($updateTime);

        $this->assertSame($createTime, $this->attribute->getCreateTime());
        $this->assertSame($updateTime, $this->attribute->getUpdateTime());
    }

    /**
     * 测试时间设置为null
     */
    public function testTimeSettersWithNull(): void
    {
        $this->attribute->setCreateTime(null);
        $this->attribute->setUpdateTime(null);

        $this->assertNull($this->attribute->getCreateTime());
        $this->assertNull($this->attribute->getUpdateTime());
    }
}
