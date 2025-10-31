<?php

namespace Tourze\UserAttributeBundle\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Serializer\Attribute\Groups;
use Symfony\Component\Serializer\Attribute\Ignore;
use Symfony\Component\Validator\Constraints as Assert;
use Tourze\Arrayable\AdminArrayInterface;
use Tourze\Arrayable\ApiArrayInterface;
use Tourze\DoctrineIpBundle\Traits\IpTraceableAware;
use Tourze\DoctrineSnowflakeBundle\Traits\SnowflakeKeyAware;
use Tourze\DoctrineTimestampBundle\Traits\TimestampableAware;
use Tourze\DoctrineUserBundle\Traits\BlameableAware;
use Tourze\UserAttributeBundle\Repository\UserAttributeRepository;

/**
 * @implements ApiArrayInterface<string, mixed>
 * @implements AdminArrayInterface<string, mixed>
 */
#[ORM\Entity(repositoryClass: UserAttributeRepository::class)]
#[ORM\Table(name: 'biz_user_attribute', options: ['comment' => '用户属性'])]
#[ORM\UniqueConstraint(name: 'biz_user_attribute_idx_uniq', columns: ['user_id', 'name'])]
class UserAttribute implements \Stringable, ApiArrayInterface, AdminArrayInterface
{
    use SnowflakeKeyAware;
    use TimestampableAware;
    use BlameableAware;
    use IpTraceableAware;

    #[ORM\ManyToOne(targetEntity: UserInterface::class)]
    #[ORM\JoinColumn(onDelete: 'CASCADE')]
    private ?UserInterface $user = null;

    #[Assert\NotBlank]
    #[Assert\Length(max: 120)]
    #[Groups(groups: ['restful_read'])]
    #[ORM\Column(type: Types::STRING, length: 120, options: ['comment' => '属性名'])]
    private string $name;

    #[Assert\Length(max: 65535)]
    #[Groups(groups: ['restful_read'])]
    #[ORM\Column(type: Types::TEXT, options: ['comment' => '值'])]
    private ?string $value = null;

    #[Assert\Length(max: 1000)]
    #[ORM\Column(type: Types::STRING, length: 1000, nullable: true, options: ['comment' => '备注'])]
    private ?string $remark = null;

    public function __toString(): string
    {
        if (null === $this->getId()) {
            return '';
        }

        return sprintf('UserAttribute %s (%s)', $this->getId(), $this->getName());
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }

    public function getValue(): ?string
    {
        return $this->value;
    }

    public function setValue(string $value): void
    {
        $this->value = $value;
    }

    public function getRemark(): ?string
    {
        return $this->remark;
    }

    public function setRemark(?string $remark): void
    {
        $this->remark = $remark;
    }

    public function getUser(): ?UserInterface
    {
        return $this->user;
    }

    public function setUser(?UserInterface $user): void
    {
        $this->user = $user;
    }

    /**
     * @return array<string, mixed>
     */
    public function retrieveApiArray(): array
    {
        return [
            'id' => $this->getId(),
            'name' => $this->getName(),
            'value' => $this->getValue(),
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public function retrieveAdminArray(): array
    {
        return [
            'id' => $this->getId(),
            'name' => $this->getName(),
            'value' => $this->getValue(),
            'remark' => $this->getRemark(),
        ];
    }
}
