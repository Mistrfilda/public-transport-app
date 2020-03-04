<?php


declare(strict_types=1);

use Doctrine\Migrations\DependencyFactory;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Tools\SchemaTool;
use Nettrine\Migrations\ContainerAwareConfiguration;
use Test\Integration\Bootstrap;

require __DIR__ . '/../../vendor/autoload.php';

$container = Bootstrap::boot();

echo 'Droping database schema' . PHP_EOL;
/** @var EntityManagerInterface $entityManager */
$entityManager = $container->getByType(EntityManagerInterface::class);

$schemaTool = new SchemaTool($entityManager);
$schemaTool->dropDatabase();

/** @var ContainerAwareConfiguration $migrationsConfiguration */
$migrationsConfiguration = $container->getByType(ContainerAwareConfiguration::class);
$migrationsConfiguration->createMigrationTable();

$migrationsConfiguration->setMigrationsDirectory(__DIR__ . '/../../migrations');

echo 'Running migrations' . PHP_EOL;
$migrator = (new DependencyFactory($migrationsConfiguration))->getMigrator();
$migrator->migrate();

echo 'Finished' . PHP_EOL;
exit(0);
