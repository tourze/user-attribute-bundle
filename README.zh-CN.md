# User Attribute Bundle

[![PHP Version](https://img.shields.io/badge/php-%5E8.1-787CB5)](https://php.net)
[![License](https://img.shields.io/badge/license-MIT-green)](LICENSE)
[![Build Status](https://img.shields.io/badge/build-passing-brightgreen)](https://github.com/tourze/php-monorepo)
[![Code Coverage](https://img.shields.io/badge/coverage-100%25-brightgreen)](https://github.com/tourze/php-monorepo)

[English](README.md) | [中文](README.zh-CN.md)

一个用于管理用户属性的 Symfony 包，支持键值存储、IP 追踪和管理界面集成。

## 特性

- 用户属性的键值存储
- 用户特定的属性管理
- 操作IP追踪
- 雪花ID生成
- 管理菜单集成
- REST API 支持
- Doctrine ORM 集成

## 安装

```bash
composer require tourze/user-attribute-bundle
```

## 快速开始

1. 将包添加到您的 `config/bundles.php`：

```php
return [
    // ...
    Tourze\UserAttributeBundle\UserAttributeBundle::class => ['all' => true],
];
```

2. 运行迁移以创建数据库表：

```bash
php bin/console doctrine:migrations:migrate
```

3. 该包将自动注册服务和管理菜单项。

## 配置

该包使用默认配置，具有以下特性：

- **实体**: `UserAttribute` 具有 IP 追踪、时间戳和责任追踪
- **仓储**: `UserAttributeRepository` 用于数据操作
- **管理菜单**: 与 EasyAdmin 菜单系统自动集成
- **数据库表**: `biz_user_attribute` 在 `user_id` 和 `name` 上具有唯一约束

## 使用方法

### 操作用户属性

```php
use Tourze\UserAttributeBundle\Entity\UserAttribute;
use Tourze\UserAttributeBundle\Repository\UserAttributeRepository;

// 创建新的用户属性
$attribute = new UserAttribute();
$attribute->setUser($user);
$attribute->setName('preferred_language');
$attribute->setValue('zh-CN');
$attribute->setRemark('用户的语言偏好设置');

$entityManager->persist($attribute);
$entityManager->flush();

// 检索用户属性
$repository = $entityManager->getRepository(UserAttribute::class);
$attributes = $repository->findBy(['user' => $user]);
```

### REST API 集成

实体支持带有组的REST API序列化：

```php
// API 响应格式
$apiData = $attribute->retrieveApiArray();
// 返回: ['id' => 123, 'name' => 'preferred_language', 'value' => 'zh-CN']

// 管理界面格式
$adminData = $attribute->retrieveAdminArray();
// 返回: ['id' => 123, 'name' => 'preferred_language', 'value' => 'zh-CN', 'remark' => '用户偏好']
```

### 管理菜单集成

该包会自动在管理界面的"用户模块"下添加"用户属性管理"菜单项。

## 高级用法

### 自定义仓储方法

您可以扩展仓储以添加自定义查询方法：

```php
// 在您的应用程序中
class CustomUserAttributeRepository extends UserAttributeRepository
{
    public function findAttributesByPrefix(UserInterface $user, string $prefix): array
    {
        return $this->createQueryBuilder('ua')
            ->where('ua.user = :user')
            ->andWhere('ua.name LIKE :prefix')
            ->setParameter('user', $user)
            ->setParameter('prefix', $prefix . '%')
            ->getQuery()
            ->getResult();
    }
}
```

### 批量操作

对于性能关键的操作，考虑批量更新：

```php
// 批量更新用户属性
$qb = $entityManager->createQueryBuilder()
    ->update(UserAttribute::class, 'ua')
    ->set('ua.value', ':newValue')
    ->where('ua.user = :user')
    ->andWhere('ua.name = :name')
    ->setParameter('newValue', $newValue)
    ->setParameter('user', $user)
    ->setParameter('name', $attributeName);

$qb->getQuery()->execute();
```

### 事件处理

您可以监听 Doctrine 事件来实现自定义逻辑：

```php
use Doctrine\ORM\Events;
use Doctrine\Bundle\DoctrineBundle\Attribute\AsDoctrineListener;

#[AsDoctrineListener(event: Events::prePersist)]
class UserAttributeListener
{
    public function prePersist(UserAttribute $attribute): void
    {
        // 保存前的自定义逻辑
        if ($attribute->getName() === 'sensitive_data') {
            $attribute->setValue(hash('sha256', $attribute->getValue()));
        }
    }
}
```

## 依赖项

- PHP 8.1+
- Symfony 7.3+
- Doctrine ORM 3.0+
- KnpMenu 3.7+
- EasyAdmin 4.0+
- 各种 tourze/* 包用于扩展功能

## 贡献

有关如何为此项目做出贡献的详细信息，请参阅 [CONTRIBUTING.md](CONTRIBUTING.md)。

## 许可证

该包在 MIT 许可证下发布。有关详细信息，请参阅 [LICENSE](LICENSE) 文件。
