<?php

namespace Tourze\UserAttributeBundle\Service;

use Knp\Menu\ItemInterface;
use Symfony\Component\DependencyInjection\Attribute\Autoconfigure;
use Tourze\EasyAdminMenuBundle\Service\LinkGeneratorInterface;
use Tourze\EasyAdminMenuBundle\Service\MenuProviderInterface;
use Tourze\UserAttributeBundle\Entity\UserAttribute;

/**
 * 业务用户系统菜单服务
 */
#[Autoconfigure(public: true)]
readonly class AdminMenu implements MenuProviderInterface
{
    public function __construct(
        private LinkGeneratorInterface $linkGenerator,
    ) {
    }

    public function __invoke(ItemInterface $item): void
    {
        if (null === $item->getChild('用户模块')) {
            $item->addChild('用户模块');
        }

        $userMenu = $item->getChild('用户模块');

        // 用户属性管理菜单
        $userMenu?->addChild('用户属性管理')
            ->setUri($this->linkGenerator->getCurdListPage(UserAttribute::class))
            ->setAttribute('icon', 'fas fa-user-cog')
        ;
    }
}
