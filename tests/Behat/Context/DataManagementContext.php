<?php

namespace App\Tests\Behat\Context;

use App\DataFixtures\AppFixtures;
use Behat\Behat\Context\Context;
use Behat\Testwork\Hook\Scope\AfterSuiteScope;
use Behat\Testwork\Hook\Scope\BeforeSuiteScope;
use Doctrine\Common\DataFixtures\Executor\ORMExecutor;
use Doctrine\Common\DataFixtures\Loader;
use Doctrine\Common\DataFixtures\Purger\ORMPurger;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpKernel\KernelInterface;

class DataManagementContext implements Context
{
    private static ?Filesystem $filesystem = null;
    private const DATABASE_LOCATION = __DIR__ . '/../../../var/test.db';
    private const DATABASE_LOCATION_COPY = __DIR__ . '/../../../var/database_test.sqlite.original';

    public function __construct(private readonly KernelInterface $kernel) {}

    /**
     * @BeforeSuite
     */
    public static function saveTestDatabase(BeforeSuiteScope $scope): void
    {
        $filesystem = self::getFilesystem();
        $filesystem->copy(
            self::DATABASE_LOCATION,
            self::DATABASE_LOCATION_COPY
        );
    }

    /**
     * @AfterSuite
     */
    public static function removeCopiedDatabase(AfterSuiteScope $scope): void
    {
        $filesystem = self::getFilesystem();
        $filesystem->copy(
            self::DATABASE_LOCATION,
            self::DATABASE_LOCATION_COPY
        );
    }

    public function theDatabaseIsInitialized(): void
    {
        $loader = new Loader();
        $loader->addFixture(new AppFixtures());

        $purger = new ORMPurger($this->getEntityManager());
        $executor = new ORMExecutor($this->getEntityManager(), $purger);

        $executor->execute($loader->getFixtures());
    }

    /**
     * @database
     */
    public function resetDatabase(): void
    {
        $filesystem = self::getFilesystem();
        $filesystem->remove(self::DATABASE_LOCATION);
        $filesystem->copy(
            self::DATABASE_LOCATION_COPY,
            self::DATABASE_LOCATION
        );
        $filesystem->chmod(self::DATABASE_LOCATION, 0777);
        $filesystem->chown(self::DATABASE_LOCATION, 1000);
        $filesystem->chgrp(self::DATABASE_LOCATION, 1000);
        $this->theDatabaseIsInitialized();
    }

    private static function getFilesystem(): Filesystem
    {
        if (self::$filesystem !== null) {
            return self::$filesystem;
        }

        return self::$filesystem = new Filesystem();
    }

    private function getEntityManager(): EntityManagerInterface
    {
        return $em = $this->kernel->getContainer()->get('doctrine.orm.default_entity_manager');
    }
}
