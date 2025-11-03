<?php

namespace Tourze\UserAttributeBundle\Tests\Repository;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use Tourze\PHPUnitSymfonyKernelTest\AbstractRepositoryTestCase;
use Tourze\UserAttributeBundle\Entity\UserAttribute;
use Tourze\UserAttributeBundle\Repository\UserAttributeRepository;

/**
 * @internal
 */
#[CoversClass(UserAttributeRepository::class)]
#[RunTestsInSeparateProcesses]
final class UserAttributeRepositoryTest extends AbstractRepositoryTestCase
{
    private UserAttributeRepository $repository;

    protected function onSetUp(): void
    {
        $this->repository = self::getService(UserAttributeRepository::class);
    }

    public function testCanBeInstantiated(): void
    {
        $this->assertInstanceOf(UserAttributeRepository::class, $this->repository);
    }

    public function testFindOneByWithOrderingShouldRespectOrder(): void
    {
        $user = $this->createNormalUser('test@example.com', 'password123');

        $attribute1 = new UserAttribute();
        $attribute1->setUser($user);
        $attribute1->setName('name_1');
        $attribute1->setValue('z_value');
        $this->repository->save($attribute1);

        $attribute2 = new UserAttribute();
        $attribute2->setUser($user);
        $attribute2->setName('name_2');
        $attribute2->setValue('a_value');
        $this->repository->save($attribute2);

        $ascResult = $this->repository->findOneBy(['user' => $user], ['value' => 'ASC']);
        $this->assertNotNull($ascResult);
        $this->assertEquals('a_value', $ascResult->getValue());

        $descResult = $this->repository->findOneBy(['user' => $user], ['value' => 'DESC']);
        $this->assertNotNull($descResult);
        $this->assertEquals('z_value', $descResult->getValue());
    }

    public function testQueryWithUserAssociation(): void
    {
        $user1 = $this->createNormalUser('user1@example.com', 'password123');
        $user2 = $this->createNormalUser('user2@example.com', 'password123');

        $attribute1 = new UserAttribute();
        $attribute1->setUser($user1);
        $attribute1->setName('attr1');
        $attribute1->setValue('value1');
        $this->repository->save($attribute1);

        $attribute2 = new UserAttribute();
        $attribute2->setUser($user2);
        $attribute2->setName('attr2');
        $attribute2->setValue('value2');
        $this->repository->save($attribute2);

        $user1Attributes = $this->repository->findBy(['user' => $user1]);
        $this->assertCount(1, $user1Attributes);
        $this->assertEquals('attr1', $user1Attributes[0]->getName());
    }

    public function testCountWithUserAssociation(): void
    {
        $user1 = $this->createNormalUser('user1@example.com', 'password123');
        $user2 = $this->createNormalUser('user2@example.com', 'password123');

        for ($i = 0; $i < 3; ++$i) {
            $attribute = new UserAttribute();
            $attribute->setUser($user1);
            $attribute->setName('attr' . $i);
            $attribute->setValue('value' . $i);
            $this->repository->save($attribute);
        }

        $attribute = new UserAttribute();
        $attribute->setUser($user2);
        $attribute->setName('attr');
        $attribute->setValue('value');
        $this->repository->save($attribute);

        $user1Count = $this->repository->count(['user' => $user1]);
        $this->assertEquals(3, $user1Count);

        $user2Count = $this->repository->count(['user' => $user2]);
        $this->assertEquals(1, $user2Count);
    }

    public function testQueryWithNullableFields(): void
    {
        $user = $this->createNormalUser('test@example.com', 'password123');

        $attribute1 = new UserAttribute();
        $attribute1->setUser($user);
        $attribute1->setName('attr1');
        $attribute1->setValue('value1');
        $attribute1->setRemark('remark1');
        $this->repository->save($attribute1);

        $attribute2 = new UserAttribute();
        $attribute2->setUser($user);
        $attribute2->setName('attr2');
        $attribute2->setValue('value2');
        $this->repository->save($attribute2);

        $withRemarks = $this->repository->createQueryBuilder('ua')
            ->where('ua.remark IS NOT NULL')
            ->andWhere('ua.user = :user')
            ->setParameter('user', $user)
            ->getQuery()
            ->getResult()
        ;
        $this->assertIsArray($withRemarks);
        $this->assertCount(1, $withRemarks);

        $withoutRemarks = $this->repository->createQueryBuilder('ua')
            ->where('ua.remark IS NULL')
            ->andWhere('ua.user = :user')
            ->setParameter('user', $user)
            ->getQuery()
            ->getResult()
        ;
        $this->assertIsArray($withoutRemarks);
        $this->assertCount(1, $withoutRemarks);
    }

    public function testCountWithNullableFields(): void
    {
        $user = $this->createNormalUser('test@example.com', 'password123');

        $attribute1 = new UserAttribute();
        $attribute1->setUser($user);
        $attribute1->setName('attr1');
        $attribute1->setValue('value1');
        $attribute1->setRemark('remark1');
        $this->repository->save($attribute1);

        $attribute2 = new UserAttribute();
        $attribute2->setUser($user);
        $attribute2->setName('attr2');
        $attribute2->setValue('value2');
        $this->repository->save($attribute2);

        $withRemarksCount = $this->repository->createQueryBuilder('ua')
            ->select('COUNT(ua.id)')
            ->where('ua.remark IS NOT NULL')
            ->andWhere('ua.user = :user')
            ->setParameter('user', $user)
            ->getQuery()
            ->getSingleScalarResult()
        ;
        $this->assertEquals(1, $withRemarksCount);

        $withoutRemarksCount = $this->repository->createQueryBuilder('ua')
            ->select('COUNT(ua.id)')
            ->where('ua.remark IS NULL')
            ->andWhere('ua.user = :user')
            ->setParameter('user', $user)
            ->getQuery()
            ->getSingleScalarResult()
        ;
        $this->assertEquals(1, $withoutRemarksCount);
    }

    public function testSaveMethodPersistsEntity(): void
    {
        $user = $this->createNormalUser('test@example.com', 'password123');

        $attribute = new UserAttribute();
        $attribute->setUser($user);
        $attribute->setName('test_attribute');
        $attribute->setValue('test_value');

        $this->repository->save($attribute);

        $this->assertNotNull($attribute->getId());

        $found = $this->repository->find($attribute->getId());
        $this->assertNotNull($found);
        $this->assertEquals('test_attribute', $found->getName());
    }

    public function testRemoveMethodDeletesEntity(): void
    {
        $user = $this->createNormalUser('test@example.com', 'password123');

        $attribute = new UserAttribute();
        $attribute->setUser($user);
        $attribute->setName('test_attribute');
        $attribute->setValue('test_value');

        $this->repository->save($attribute);
        $id = $attribute->getId();

        $this->repository->remove($attribute);

        $found = $this->repository->find($id);
        $this->assertNull($found);
    }

    public function testCountWithUserAssociationQueries(): void
    {
        $user1 = $this->createNormalUser('user1@example.com', 'password123');
        $user2 = $this->createNormalUser('user2@example.com', 'password123');

        for ($i = 0; $i < 2; ++$i) {
            $attribute = new UserAttribute();
            $attribute->setUser($user1);
            $attribute->setName('attr' . $i);
            $attribute->setValue('value' . $i);
            $this->repository->save($attribute);
        }

        $count = $this->repository->count(['user' => $user1]);
        $this->assertEquals(2, $count);

        $count = $this->repository->count(['user' => $user2]);
        $this->assertEquals(0, $count);
    }

    public function testQueryWithUserAssociationQueries(): void
    {
        $user = $this->createNormalUser('test@example.com', 'password123');

        $attribute = new UserAttribute();
        $attribute->setUser($user);
        $attribute->setName('test_attr');
        $attribute->setValue('test_value');
        $this->repository->save($attribute);

        $results = $this->repository->createQueryBuilder('ua')
            ->join('ua.user', 'u')
            ->where('u.userIdentifier = :userIdentifier')
            ->setParameter('userIdentifier', 'test@example.com')
            ->getQuery()
            ->getResult()
        ;

        $this->assertIsArray($results);
        $this->assertCount(1, $results);
        $this->assertInstanceOf(UserAttribute::class, $results[0]);
        $this->assertEquals('test_attr', $results[0]->getName());
    }

    public function testQueryWithValueIsNull(): void
    {
        $user = $this->createNormalUser('test@example.com', 'password123');

        $attribute1 = new UserAttribute();
        $attribute1->setUser($user);
        $attribute1->setName('attr1');
        $attribute1->setValue('some_value');
        $this->repository->save($attribute1);

        $attribute2 = new UserAttribute();
        $attribute2->setUser($user);
        $attribute2->setName('attr2');
        $attribute2->setValue('');
        $this->repository->save($attribute2);

        $emptyValueResults = $this->repository->createQueryBuilder('ua')
            ->where('ua.value = :empty')
            ->setParameter('empty', '')
            ->getQuery()
            ->getResult()
        ;

        $this->assertIsArray($emptyValueResults);
        $this->assertCount(1, $emptyValueResults);
        $this->assertInstanceOf(UserAttribute::class, $emptyValueResults[0]);
        $this->assertEquals('attr2', $emptyValueResults[0]->getName());
    }

    public function testQueryWithRemarkIsNull(): void
    {
        $user = $this->createNormalUser('test@example.com', 'password123');

        $attribute1 = new UserAttribute();
        $attribute1->setUser($user);
        $attribute1->setName('attr1');
        $attribute1->setValue('value1');
        $attribute1->setRemark('some_remark');
        $this->repository->save($attribute1);

        $attribute2 = new UserAttribute();
        $attribute2->setUser($user);
        $attribute2->setName('attr2');
        $attribute2->setValue('value2');
        $this->repository->save($attribute2);

        $nullRemarkResults = $this->repository->createQueryBuilder('ua')
            ->where('ua.remark IS NULL')
            ->getQuery()
            ->getResult()
        ;

        $this->assertIsArray($nullRemarkResults);
        $this->assertCount(1, $nullRemarkResults);
        $this->assertInstanceOf(UserAttribute::class, $nullRemarkResults[0]);
        $this->assertEquals('attr2', $nullRemarkResults[0]->getName());
    }

    public function testCountWithValueIsNull(): void
    {
        $user = $this->createNormalUser('test@example.com', 'password123');

        $attribute = new UserAttribute();
        $attribute->setUser($user);
        $attribute->setName('attr_with_empty_value');
        $attribute->setValue('');
        $this->repository->save($attribute);

        $count = $this->repository->createQueryBuilder('ua')
            ->select('COUNT(ua.id)')
            ->where('ua.value = :empty')
            ->setParameter('empty', '')
            ->getQuery()
            ->getSingleScalarResult()
        ;

        $this->assertEquals(1, $count);
    }

    public function testCountWithRemarkIsNull(): void
    {
        $user = $this->createNormalUser('test@example.com', 'password123');

        $attribute = new UserAttribute();
        $attribute->setUser($user);
        $attribute->setName('attr_with_null_remark');
        $attribute->setValue('some_value');
        $this->repository->save($attribute);

        $count = $this->repository->createQueryBuilder('ua')
            ->select('COUNT(ua.id)')
            ->where('ua.remark IS NULL')
            ->getQuery()
            ->getSingleScalarResult()
        ;

        $this->assertEquals(1, $count);
    }

    protected function createNewEntity(): object
    {
        $user = $this->createNormalUser('test@example.com', 'password123');

        $entity = new UserAttribute();
        $entity->setUser($user);
        $entity->setName('test_attribute_' . uniqid());
        $entity->setValue('test_value');
        $entity->setRemark('Test attribute for repository testing');

        return $entity;
    }

    protected function getRepository(): UserAttributeRepository
    {
        return $this->repository;
    }

    public function testFindOneByAssociationUserShouldReturnMatchingEntity(): void
    {
        $user1 = $this->createNormalUser('user1@example.com', 'password123');
        $user2 = $this->createNormalUser('user2@example.com', 'password123');

        $attr1 = new UserAttribute();
        $attr1->setUser($user1);
        $attr1->setName('attr1');
        $attr1->setValue('value1');
        $this->repository->save($attr1);

        $attr2 = new UserAttribute();
        $attr2->setUser($user2);
        $attr2->setName('attr2');
        $attr2->setValue('value2');
        $this->repository->save($attr2);

        $result = $this->repository->findOneBy(['user' => $user1]);
        $this->assertNotNull($result);
        $this->assertEquals('attr1', $result->getName());
        $this->assertEquals($user1, $result->getUser());
    }

    public function testCountByAssociationUserShouldReturnCorrectNumber(): void
    {
        $user1 = $this->createNormalUser('user1@example.com', 'password123');
        $user2 = $this->createNormalUser('user2@example.com', 'password123');

        for ($i = 0; $i < 3; ++$i) {
            $attr = new UserAttribute();
            $attr->setUser($user1);
            $attr->setName('attr' . $i);
            $attr->setValue('value' . $i);
            $this->repository->save($attr);
        }

        $attr = new UserAttribute();
        $attr->setUser($user2);
        $attr->setName('attr');
        $attr->setValue('value');
        $this->repository->save($attr);

        $count = $this->repository->count(['user' => $user1]);
        $this->assertEquals(3, $count);

        $count = $this->repository->count(['user' => $user2]);
        $this->assertEquals(1, $count);
    }
}
