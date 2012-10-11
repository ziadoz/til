<?php
/**
 * Doctrine DBAL Examples:
 */
require_once __DIR__ . '/../vendor/autoload.php';

use Doctrine\DBAL\Configuration;
use Doctrine\DBAL\DriverManager;
use Doctrine\DBAL\Schema\Schema;
use Doctrine\DBAL\Platforms\MySqlPlatform;

$connection = DriverManager::getConnection(array(
    'dbname' => 'test',
    'user' => 'root',
    'password' => '',
    'host' => 'localhost',
    'driver' => 'pdo_mysql',
    'charset' => 'utf8',
    'driverOptions' => array(
        PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8'
    )
), $config = new Configuration);

/**
 * Objects.
 */
$databasePlatform = $connection->getDatabasePlatform();
$schemaManager = $connection->getSchemaManager();
$queryBuilder = $connection->createQueryBuilder();

/**
 * Table Details
 */
$details = $schemaManager->listTableDetails('test');

/**
 * Insert Record
 */
$results = $connection->insert('test', array(
    'title' => 'Hello, World!', 
    'content' => 'This is some awesome text of awesomeness.'
));

/**
 * Fetch Records
 */
$records = $connection->fetchAll('SELECT * FROM test');
foreach ($records as $record) {
    print_r($record);
}

/**
 * Query Builder
 * See: https://github.com/doctrine/dbal/blob/master/lib/Doctrine/DBAL/Query/QueryBuilder.php
 */
$results = $queryBuilder->select('*')
                         ->from('test', 't')
                         ->orderBy('t.title', 'ASC')
                         ->execute()
                         ->fetchAll();

/**
 * Schema Builder
 */
$schema = new Schema;
$table = $schema->createTable('more_test');
$table->addColumn('id', 'integer', array('unsigned' => true));
$table->addColumn('title', 'string', array('length' => 128));
$table->addColumn('content', 'text');
$table->setPrimaryKey(array('id'));

$queries = $schema->toSql($databasePlatform);
$drops = $schema->toDropSql($databasePlatform);