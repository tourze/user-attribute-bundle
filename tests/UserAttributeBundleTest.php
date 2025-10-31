<?php

declare(strict_types=1);

namespace Tourze\UserAttributeBundle\Tests;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use Tourze\PHPUnitSymfonyKernelTest\AbstractBundleTestCase;
use Tourze\UserAttributeBundle\UserAttributeBundle;

/**
 * @internal
 */
#[CoversClass(UserAttributeBundle::class)]
#[RunTestsInSeparateProcesses]
final class UserAttributeBundleTest extends AbstractBundleTestCase
{
}
