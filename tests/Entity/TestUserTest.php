<?php

namespace Tourze\UserAttributeBundle\Tests\Entity;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Tourze\PHPUnitDoctrineEntity\AbstractEntityTestCase;
use Tourze\UserAttributeBundle\Entity\TestUser;

/**
 * @internal
 */
#[CoversClass(TestUser::class)]
final class TestUserTest extends AbstractEntityTestCase
{
    protected function createEntity(): object
    {
        return new TestUser();
    }

    /** @return iterable<string, array{string, mixed}> */
    public static function propertiesProvider(): iterable
    {
        return [
            'identifier' => ['identifier', 'test_value'],
            'roles' => ['roles', ['key' => 'value']],
        ];
    }

    public function testGetId(): void
    {
        $user = new TestUser();
        // TestUser ID is always null as this is a test entity
        $this->assertEquals(null, $user->getId());
    }

    public function testGetUserIdentifier(): void
    {
        $user = new TestUser();
        $user->setIdentifier('test-user');
        $this->assertEquals('test-user', $user->getUserIdentifier());
    }

    public function testGetRoles(): void
    {
        $user = new TestUser();
        $roles = $user->getRoles();
        $this->assertContains('ROLE_USER', $roles);
    }

    public function testToString(): void
    {
        $user = new TestUser();
        $user->setIdentifier('test-user');
        $this->assertEquals('test-user', (string) $user);
    }
}
