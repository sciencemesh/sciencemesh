<?php
// bootstrap_doctrine.php

// See :doc:`Configuration <../reference/configuration>` for up to date autoloading details.
use Doctrine\ORM\Tools\Setup;
use Doctrine\Common\EventManager;
use Doctrine\DBAL\Event\Listeners\OracleSessionInit;

// Load Doctrine (via composer OR pear):
// Via composer
// -------------
// If you have installed doctrine using composer into a vendor dir
// (either as a project specific dependency or globally), then include doctrine
// using the composer autoload:
require_once  "/usr/local/doctrine/vendor/autoload.php";

// Via pear as a global install
// ----------------------------
// If you have installed doctrine globally using pear, then require the
// Setup.php and use AutoloadPEAR:
// require_once "Doctrine/ORM/Tools/Setup.php";
// Setup::registerAutoloadDirectory("/usr/share/php/");

$evm = new EventManager();
// Create a simple "default" Doctrine ORM configuration for XML Mapping
$isDevMode = true;
//$config = Setup::createXMLMetadataConfiguration(array(__DIR__."/config/xml"), $isDevMode);
// or if you prefer yaml or annotations
$config = Setup::createAnnotationMetadataConfiguration(array(__DIR__."/entities"), $isDevMode);
//$config = Setup::createYAMLMetadataConfiguration(array(__DIR__."/config/yaml"), $isDevMode);

//
// If you intend to use APC cache (recommended in production), then you need to
// specify the following two lines and install APC cache (see GOCDB wiki):
//$config->setMetadataCacheImpl(new \Doctrine\Common\Cache\ApcCache());
//$config->setQueryCacheImpl(new \Doctrine\Common\Cache\ApcCache());
//
// By default, Doctrine will automatically compile Doctrine proxy objects
// into the system's tmp dir. This is not recommended for production.
// For production, you can specify where these proxy objects should be stored
// using "$config->setProxyDir('pathToYourProxyDir');"
// If you specify the ProxyDir, then you also need to manually compile your proxy objects
// into the specified ProxyDir using the following command:
// 'doctrine orm:generate-proxies compiledEntities'
//$config->setProxyDir(__DIR__.'/compiledEntities');
//


    ///////////////////////SQLITE CONNECTION DETAILS/////////////////////////////////////////////
    // $conn = array(
    // 	'driver' => 'pdo_sqlite',
    // 	'path' => __DIR__ . '/db.sqlite',
    // );
    /////////////////////////////////////////////////////////////////////////////////////////////


    ///////////////////////ORACLE CONNECTION DETAILS////////////////////////////////////////////
    //	$conn = array(
    //		'driver' => 'oci8',
    //		'user' => 'Doctrine',
    //		'password' => 'doc',
    //		'host' => 'localhost',
    //		'port' => 1521,
    //		'dbname' => 'XE',
    //		'charset' => 'AL32UTF8'
    //	);
    //
    // ** For Oracle installations uncomment the following line:
    // $evm->addEventSubscriber(new OracleSessionInit(array('NLS_TIME_FORMAT' => 'HH24:MI:SS')));
    /////////////////////////////////////////////////////////////////////////////////////////////


    ///////////////////////MYSQL CONNECTION DETAILS////////////////////////////////////////////
    $gocdb_db_server = getenv("GOCDB_DATABASE_SERVER");

    if ($gocdb_db_server == false)
        die('The environment variable GOCDB_DATABASE_SERVER has not been set.');

    $conn = array(
    	'driver' => 'pdo_mysql',
    	'user' => 'gocdbuser',
    	'password' => 'srgh85tz7sdgz2',
    	'host' => $gocdb_db_server,
    	'dbname' => 'gocdb'
    );
    /////////////////////////////////////////////////////////////////////////////////////////////


// obtaining the entity manager
$entityManager = \Doctrine\ORM\EntityManager::create($conn, $config, $evm);
