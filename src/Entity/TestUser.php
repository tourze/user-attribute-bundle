<?php

declare(strict_types=1);

namespace Tourze\UserAttributeBundle\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;
use Tourze\UserAttributeBundle\Exception\UserIdentifierException;

/**
 * 测试用户实体
 *
 * 仅用于 DataFixtures 中创建测试数据
 */
#[ORM\Entity]
#[ORM\Table(name: 'test_user', options: ['comment' => '测试用户表'])]
class TestUser implements UserInterface, \Stringable
{
    /**
     * @var int|null 主键ID
     */
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: Types::INTEGER, options: ['comment' => '主键ID'])]
    private ?int $id = null;

    #[ORM\Column(type: Types::STRING, length: 180, unique: true, options: ['comment' => '用户标识符'])]
    #[Assert\Length(max: 180)]
    #[Assert\NotBlank]
    private string $identifier;

    /** @var array<string> */
    #[ORM\Column(type: Types::JSON, options: ['comment' => '用户角色'])]
    #[Assert\NotNull]
    private array $roles = ['ROLE_USER'];

    public function __construct()
    {
        $this->identifier = 'test-user';
    }

    public function __toString(): string
    {
        return $this->identifier;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    /** @return array<string> */
    public function getRoles(): array
    {
        return $this->roles;
    }

    public function eraseCredentials(): void
    {
        // 清除敏感数据
    }

    public function getUserIdentifier(): string
    {
        if ('' === $this->identifier) {
            throw new UserIdentifierException('User identifier cannot be empty');
        }

        return $this->identifier;
    }

    public function getIdentifier(): string
    {
        return $this->identifier;
    }

    public function setIdentifier(string $identifier): void
    {
        $this->identifier = $identifier;
    }

    /** @param array<string> $roles */
    public function setRoles(array $roles): void
    {
        $this->roles = $roles;
    }
}
