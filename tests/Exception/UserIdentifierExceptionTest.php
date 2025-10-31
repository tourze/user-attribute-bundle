<?php

declare(strict_types=1);

namespace Tourze\UserAttributeBundle\Tests\Exception;

use PHPUnit\Framework\Attributes\CoversClass;
use Tourze\PHPUnitBase\AbstractExceptionTestCase;
use Tourze\UserAttributeBundle\Exception\UserIdentifierException;

/**
 * @internal
 */
#[CoversClass(UserIdentifierException::class)]
final class UserIdentifierExceptionTest extends AbstractExceptionTestCase
{
    public function testCanBeInstantiated(): void
    {
        $exception = new UserIdentifierException('Test message');

        $this->assertInstanceOf(UserIdentifierException::class, $exception);
        $this->assertInstanceOf(\RuntimeException::class, $exception);
        $this->assertEquals('Test message', $exception->getMessage());
    }

    public function testExtendsRuntimeException(): void
    {
        $exception = new UserIdentifierException();

        $this->assertInstanceOf(\RuntimeException::class, $exception);
    }
}
