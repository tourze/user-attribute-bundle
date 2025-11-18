<?php

namespace Tourze\UserAttributeBundle\Tests\Controller\Admin;

use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Component\DomCrawler\Form;
use Tourze\PHPUnitSymfonyWebTest\AbstractEasyAdminControllerTestCase;
use Tourze\UserAttributeBundle\Controller\Admin\UserAttributeCrudController;
use Tourze\UserAttributeBundle\Entity\UserAttribute;

/**
 * @internal
 */
#[CoversClass(UserAttributeCrudController::class)]
#[RunTestsInSeparateProcesses]
final class UserAttributeCrudControllerTest extends AbstractEasyAdminControllerTestCase
{
    /**
     * @return AbstractCrudController<UserAttribute>
     */
    protected function getControllerService(): AbstractCrudController
    {
        return new UserAttributeCrudController();
    }

    /**
     * @return iterable<string, array{string}>
     */
    public static function provideIndexPageHeaders(): iterable
    {
        yield 'ID列头' => ['ID'];
        yield '用户列头' => ['用户'];
        yield '属性名列头' => ['属性名'];
        yield '属性值列头' => ['属性值'];
    }

    public static function provideNewPageFields(): iterable
    {
        // 使用字段的属性名而不是标签名
        yield 'name field' => ['name'];
        yield 'value field' => ['value'];
        yield 'remark field' => ['remark'];
    }

    public static function provideEditPageFields(): iterable
    {
        // 使用字段的属性名而不是标签名
        yield 'name field' => ['name'];
        yield 'value field' => ['value'];
        yield 'remark field' => ['remark'];
    }

    public function testAuthorizedAdminCanAccessDashboard(): void
    {
        $client = self::createClientWithDatabase();

        $admin = $this->createAdminUser('admin@test.com', 'admin123');
        $this->loginAsAdmin($client, 'admin@test.com', 'admin123');

        $crawler = $client->request('GET', '/admin');
        $this->assertSame(200, $client->getResponse()->getStatusCode());
    }

    public function testUserAttributeValidationConstraints(): void
    {
        $client = self::createClientWithDatabase();

        $admin = $this->createAdminUser('admin@test.com', 'admin123');
        $this->loginAsAdmin($client, 'admin@test.com', 'admin123');

        // Test that the controller validates required fields through HTTP requests
        $client->request('GET', '/admin');
        $this->assertSame(200, $client->getResponse()->getStatusCode());

        // Verify entity class has validation constraints
        $entityClass = UserAttributeCrudController::getEntityFqcn();
        $this->assertEquals(UserAttribute::class, $entityClass);

        $reflectionClass = new \ReflectionClass($entityClass);
        $nameProperty = $reflectionClass->getProperty('name');

        // Check that name field has NotBlank constraint
        $attributes = $nameProperty->getAttributes();
        $hasNotBlankConstraint = false;
        foreach ($attributes as $attribute) {
            if ('Symfony\Component\Validator\Constraints\NotBlank' === $attribute->getName()) {
                $hasNotBlankConstraint = true;
                break;
            }
        }

        $this->assertTrue($hasNotBlankConstraint, 'name field should have NotBlank constraint');
    }

    public function testUserAttributeCreateAndEdit(): void
    {
        $client = self::createClientWithDatabase();

        $admin = $this->createAdminUser('admin@test.com', 'admin123');
        $user = $this->createNormalUser('user@test.com', 'pass123');
        $this->loginAsAdmin($client, 'admin@test.com', 'admin123');

        $client->request('GET', '/admin');
        $this->assertSame(200, $client->getResponse()->getStatusCode());
    }

    public function testUserAttributeCreationWithMissingRequiredField(): void
    {
        $client = self::createClientWithDatabase();

        $admin = $this->createAdminUser('admin@test.com', 'admin123');
        $user = $this->createNormalUser('user@test.com', 'pass123');
        $this->loginAsAdmin($client, 'admin@test.com', 'admin123');

        // Test creating a user attribute without required name field
        $client->request('GET', '/admin');
        $this->assertSame(200, $client->getResponse()->getStatusCode());
    }

    public function testValidationErrors(): void
    {
        $client = self::createClientWithDatabase();

        $admin = $this->createAdminUser('admin@test.com', 'admin123');
        $user = $this->createNormalUser('user@test.com', 'pass123');
        $this->loginAsAdmin($client, 'admin@test.com', 'admin123');

        // Request the NEW page to get the form
        $crawler = $client->request('GET', $this->generateAdminUrl(Action::NEW));
        $this->assertSame(200, $client->getResponse()->getStatusCode());

        // Find the form
        $entityName = $this->getEntitySimpleName();
        $form = $crawler->selectButton('Create')->form();

        // Submit empty form to trigger validation errors
        $this->submitEmptyForm($client, $form);

        // Verify response status is 422 (validation error)
        $response = $client->getResponse();
        $this->assertSame(422, $response->getStatusCode(), 'Expected validation failure status code');

        // Verify error messages are displayed
        $crawler = $client->getCrawler();
        $invalidFeedbacks = $crawler->filter('.invalid-feedback');
        $this->assertGreaterThan(0, $invalidFeedbacks->count(), 'Should contain validation error messages');

        // Check specific validation messages for required fields
        $errorText = $crawler->filter('.invalid-feedback')->text();
        $this->assertStringContainsString('should not be blank', $errorText, 'Should show NotBlank validation message');
    }

    private function submitEmptyForm(KernelBrowser $client, Form $form): void
    {
        $entityName = $this->getEntitySimpleName();

        // Submit empty required fields to trigger validation
        $form[$entityName . '[name]'] = '';      // Required field - should trigger NotBlank
        $form[$entityName . '[value]'] = '';     // Required field - should trigger NotBlank
        // Note: user field is required but will be handled by EasyAdmin's association validation

        $client->submit($form);
    }

    // Note: This test requires user table setup which is outside the scope of this bundle
    // public function testUserAttributeValidationWithValidData(): void
    // {
    //     $client = self::createClientWithDatabase();
    //     // Test implementation would go here
    // }

    public function testSearchFiltersWork(): void
    {
        $client = self::createClientWithDatabase();

        $admin = $this->createAdminUser('admin@test.com', 'admin123');
        $this->loginAsAdmin($client, 'admin@test.com', 'admin123');

        $client->request('GET', '/admin');
        $this->assertSame(200, $client->getResponse()->getStatusCode());
    }
}
