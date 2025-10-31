<?php

namespace Tourze\UserAttributeBundle\Controller\Admin;

use Doctrine\ORM\EntityManagerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Attribute\AdminCrud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Filters;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Filter\EntityFilter;
use EasyCorp\Bundle\EasyAdminBundle\Filter\TextFilter;
use Symfony\Component\Security\Core\User\UserInterface;
use Tourze\UserAttributeBundle\Entity\TestUser;
use Tourze\UserAttributeBundle\Entity\UserAttribute;

/**
 * @extends AbstractCrudController<UserAttribute>
 */
#[AdminCrud(routePath: '/user/attribute', routeName: 'user_attribute')]
final class UserAttributeCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return UserAttribute::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('用户属性')
            ->setEntityLabelInPlural('用户属性管理')
            ->setDefaultSort(['id' => 'DESC'])
            ->setSearchFields(['name', 'value', 'user.username', 'user.nickName'])
            ->showEntityActionsInlined()
            ->setPageTitle(Crud::PAGE_INDEX, '用户属性管理')
            ->setPageTitle(Crud::PAGE_NEW, '创建用户属性')
            ->setPageTitle(Crud::PAGE_EDIT, '编辑用户属性')
            ->setPageTitle(Crud::PAGE_DETAIL, '用户属性详情')
        ;
    }

    public function configureFields(string $pageName): iterable
    {
        yield IdField::new('id', 'ID')->hideOnForm();

        yield AssociationField::new('user', '用户')
            ->setRequired(true)
            ->autocomplete()
            ->formatValue(fn ($value, UserAttribute $entity) => $this->formatUserDisplay($entity->getUser()))
            ->setFormTypeOption('class', TestUser::class)
        ;

        yield TextField::new('name', '属性名')
            ->setRequired(true)
            ->setHelp('属性的名称标识符')
            ->setColumns(6)
        ;

        yield TextareaField::new('value', '属性值')
            ->setRequired(true)
            ->setHelp('属性的值内容')
            ->setNumOfRows(3)
        ;

        yield TextareaField::new('remark', '备注')
            ->hideOnIndex()
            ->setHelp('对此属性的描述说明')
            ->setNumOfRows(2)
        ;

        if (Crud::PAGE_DETAIL === $pageName) {
            yield TextField::new('createdBy', '创建人')->hideOnForm();
            yield TextField::new('updatedBy', '更新人')->hideOnForm();
            yield TextField::new('createdFromIp', '创建IP')->hideOnForm();
            yield TextField::new('updatedFromIp', '更新IP')->hideOnForm();
            yield DateTimeField::new('createTime', '创建时间')->hideOnForm();
            yield DateTimeField::new('updateTime', '更新时间')->hideOnForm();
        }
    }

    public function configureFilters(Filters $filters): Filters
    {
        return $filters
            ->add(EntityFilter::new('user', '用户'))
            ->add(TextFilter::new('name', '属性名'))
            ->add(TextFilter::new('value', '属性值'))
        ;
    }

    public function configureActions(Actions $actions): Actions
    {
        return $actions
            ->add(Crud::PAGE_INDEX, Action::DETAIL)
        ;
    }

    public function persistEntity(EntityManagerInterface $entityManager, $entityInstance): void
    {
        parent::persistEntity($entityManager, $entityInstance);
        $this->addFlash('success', sprintf('用户属性"%s"创建成功！', $entityInstance->getName()));
    }

    public function updateEntity(EntityManagerInterface $entityManager, $entityInstance): void
    {
        parent::updateEntity($entityManager, $entityInstance);
        $this->addFlash('success', sprintf('用户属性"%s"更新成功！', $entityInstance->getName()));
    }

    public function deleteEntity(EntityManagerInterface $entityManager, $entityInstance): void
    {
        $name = $entityInstance->getName();
        parent::deleteEntity($entityManager, $entityInstance);
        $this->addFlash('success', sprintf('用户属性"%s"删除成功！', $name));
    }

    private function formatUserDisplay(?UserInterface $user): string
    {
        if (null === $user) {
            return '未分配';
        }

        $displayName = $this->getUserDisplayName($user);
        $username = $this->getUserIdentifier($user);

        if ('' !== $displayName && '' !== $username) {
            return sprintf('%s (%s)', $displayName, $username);
        }

        if ('' !== $username) {
            return $username;
        }

        $id = method_exists($user, 'getId') ? $user->getId() : null;
        if (null !== $id && (is_string($id) || is_int($id))) {
            return '用户ID: ' . (string) $id;
        }

        return '用户ID: 未知';
    }

    private function getUserDisplayName(UserInterface $user): string
    {
        if (method_exists($user, 'getNickName')) {
            $nickName = $user->getNickName();
            if (is_string($nickName)) {
                return $nickName;
            }

            return '';
        }

        if (method_exists($user, 'getDisplayName')) {
            $displayName = $user->getDisplayName();
            if (is_string($displayName)) {
                return $displayName;
            }

            return '';
        }

        return '';
    }

    private function getUserIdentifier(UserInterface $user): string
    {
        if (method_exists($user, 'getUsername')) {
            $username = $user->getUsername();
            if (is_string($username)) {
                return $username;
            }

            return '';
        }

        // UserInterface always has getUserIdentifier in Symfony 5.3+
        return $user->getUserIdentifier();
    }
}
