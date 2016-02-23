<?php

use Symfony\Component\Console\Application;
use Symfony\Component\Console\Input\InputOption;
use Doctrine\ORM\Tools\Console\ConsoleRunner;

$console = new Application('Kinetise Api Skeleton', '0.1 beta');

$console
    ->getDefinition()
    ->addOption(
        new InputOption('--env', '-e', InputOption::VALUE_REQUIRED, 'The Environment name.', APP_ENV)
    );

$console
    ->getDefinition()
    ->addOption(
        new InputOption('--debug', '-d', InputOption::VALUE_REQUIRED, 'The Environment name.', APP_DEBUG)
    );

$console->setHelperSet(ConsoleRunner::createHelperSet($app['orm.em']));

$console->setDispatcher($app['dispatcher']);

$console->addCommands(array(
    // DBAL Commands
    new \Doctrine\DBAL\Tools\Console\Command\RunSqlCommand(),
    new \Doctrine\DBAL\Tools\Console\Command\ImportCommand(),

    // ORM Commands
    new \Doctrine\ORM\Tools\Console\Command\ClearCache\MetadataCommand(),
    new \Doctrine\ORM\Tools\Console\Command\ClearCache\ResultCommand(),
    new \Doctrine\ORM\Tools\Console\Command\ClearCache\QueryCommand(),
    new \Doctrine\ORM\Tools\Console\Command\SchemaTool\CreateCommand(),
    new \Doctrine\ORM\Tools\Console\Command\SchemaTool\UpdateCommand(),
    new \Doctrine\ORM\Tools\Console\Command\SchemaTool\DropCommand(),
    new \Doctrine\ORM\Tools\Console\Command\EnsureProductionSettingsCommand(),
    new \Doctrine\ORM\Tools\Console\Command\ConvertDoctrine1SchemaCommand(),
    new \Doctrine\ORM\Tools\Console\Command\GenerateRepositoriesCommand(),
    new \Doctrine\ORM\Tools\Console\Command\GenerateEntitiesCommand(),
    new \Doctrine\ORM\Tools\Console\Command\GenerateProxiesCommand(),
    new \Doctrine\ORM\Tools\Console\Command\ConvertMappingCommand(),
    new \Doctrine\ORM\Tools\Console\Command\RunDqlCommand(),
    new \Doctrine\ORM\Tools\Console\Command\ValidateSchemaCommand(),
    new \Doctrine\ORM\Tools\Console\Command\InfoCommand(),
    new \Doctrine\ORM\Tools\Console\Command\MappingDescribeCommand(),
));

return $console;