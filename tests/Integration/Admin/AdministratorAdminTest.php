<?php

namespace App\Tests\Intergration\Admin;

use App\Constants;
use App\Entity\Administrator;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use LogicException;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class AdministratorAdminTest extends KernelTestCase
{
    protected EntityManagerInterface $entityManager;

    protected function setUp(): void
    {
        $this->entityManager = static::getContainer()->get('doctrine')->getManager();
    }

    public function testCreateAdmin(): void
    {
        $adminAdmin = static::getContainer()->get('admin.administrator');

        // Create a new instance of Administrator
        $admin = new Administrator();
        $admin->setEmail('john.doe@gmail.com');
        $now = new DateTime();
        $admin->setLastLoginAt($now);
        $admin->setPlainPassword('1234');

        // Set password and erase sensitive data before persisting data in database
        $adminAdmin->prePersist($admin);

        // Assert the method call was effective
        $this->assertNull($admin->getPlainPassword());
        $this->assertEquals('1234', $admin->getPassword());

        // Assert other fields values are correct
        $this->assertFalse($admin->getEnabled());
        $this->assertContains(Constants::ROLE_SUPER_ADMIN, $admin->getRoles());

        // Test updatePassword method behavior if no plainPassword
        $admin = new Administrator();
        $admin->setEmail('john.doe@gmail.com');
        $now = new DateTime();
        $admin->setLastLoginAt($now);

        $adminAdmin->prePersist($admin);

        $this->assertNull($admin->getPassword());

        // Test prePersist method behavior if not instance of Administrator
        $this->expectException(LogicException::class);
        $adminAdmin->prePersist('admin');
    }

    public function testModifyAdmin()
    {
        $adminRepository = $this->entityManager->getRepository(Administrator::class);
        $adminAdmin = static::getContainer()->get('admin.administrator');

        // Create a new instance of Administrator
        $admin = new Administrator();
        $admin->setEmail('john.doe@gmail.com');
        $now = new DateTime();
        $admin->setLastLoginAt($now);
        $admin->setPlainPassword('1234');

        $adminAdmin->preUpdate($admin);

        $this->entityManager->persist($admin);
        $this->entityManager->flush();

        // Get administrator entity, modify it and persist it
        $persistedAdmin = $adminRepository->findOneBy(['email' => 'john.doe@gmail.com']);
        $persistedAdmin->setEnabled(true);

        $this->entityManager->persist($persistedAdmin);
        $this->entityManager->flush();

        // Get the modified administrator and assert its new status
        $modifiedAdmin = $adminRepository->findOneBy(['email' => 'john.doe@gmail.com']);
        $this->assertTrue($modifiedAdmin->getEnabled());

        // Test preUpdate method behavior if not instance of Administrator
        $this->expectException(LogicException::class);
        $adminAdmin->preUpdate('admin');
    }
}
