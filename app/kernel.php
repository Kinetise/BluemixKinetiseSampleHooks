<?php

use Silex\Application;
use Igorw\Silex\ConfigServiceProvider;
use Silex\Provider\UrlGeneratorServiceProvider;
use Silex\Provider\ServiceControllerServiceProvider;
use Silex\Provider\WebProfilerServiceProvider;
use Silex\Provider\TwigServiceProvider;
use Silex\Provider\HttpFragmentServiceProvider;
use KinetiseSkeleton\Provider\ServiceProvider as KinetiseServiceProvider;
use Doctrine\Common\Annotations\AnnotationRegistry;
use Silex\Provider\DoctrineServiceProvider;
use Dflydev\Silex\Provider\DoctrineOrm\DoctrineOrmServiceProvider;
use JMS\Serializer\SerializerBuilder;
use Symfony\Component\HttpKernel\Exception\HttpException;
use KinetiseSkeleton\Response\MessageResponse;
use Silex\Provider\MonologServiceProvider;
use KinetiseSkeleton\Doctrine\Logger as KinetiseDoctrineLogger;

AnnotationRegistry::registerAutoloadNamespace(
    'JMS\Serializer\Annotation',
    APP_PATH . '/vendor/jms/serializer/src'
);

AnnotationRegistry::registerAutoloadNamespace(
    'Doctrine\ORM\Mapping',
    APP_PATH . '/vendor/doctrine/orm/lib'
);

$app = new Application();
$app['app.rootDir'] = APP_PATH;
$app['debug'] = APP_DEBUG;

$app->register(new UrlGeneratorServiceProvider());
$app->register(new ServiceControllerServiceProvider());
$app->register(new TwigServiceProvider());
$app->register(new MonologServiceProvider());
$app->register(new HttpFragmentServiceProvider());

try {
    if (isset($_ENV["VCAP_SERVICES"])) { // running in bluemix
        $vcap_services = json_decode($_ENV["VCAP_SERVICES"]);
        if (isset($vcap_services->{'mysql-5.5'})) { //if "mysql" db service is bound to this application
            $db = $vcap_services->{'mysql-5.5'}[0]->credentials;
        } else if (isset($vcap_services->{'cleardb'})) { //if cleardb mysql db service is bound to this application
            $db = $vcap_services->{'cleardb'}[0]->credentials;
        } else {
            echo "Error: No suitable MySQL database bound to the application. <br>";
            die();
        }

        $app->register(new DoctrineServiceProvider(), array(
            'db.options' => array(
                'driver' => 'pdo_mysql',
                'host' => $db->hostname,
                'port' => $db->port,
                'dbname' => $db->name,
                'user' => $db->username,
                'password' => $db->password,
                'path' => ''
            ),
        ));
    } else {
        $app->register(new DoctrineServiceProvider());
    }
} catch (Exception $e) {
    die(var_dump($e->getMessage()));
}


$app->register(new KinetiseServiceProvider());
$app->register(new DoctrineOrmServiceProvider());

// initialize serializer
$app['jms.serializer'] = $app->share(function () use ($app) {
    $builder = SerializerBuilder::create();
    $builder->addDefaultDeserializationVisitors();
    $builder->addDefaultSerializationVisitors();
    $builder->addDefaultHandlers();
    $builder->addDefaultListeners();
    $builder->setCacheDir($app['app.cacheDir']);
    $builder->addMetadataDir($app['app.cacheDir']);
    $builder->setDebug($app['debug']);

    return $builder->build();
});

$app['db.config'] = $app->extend('db.config', function ($config, $app) {
    $config->setSQLLogger(
        new KinetiseDoctrineLogger($app['monolog'])
    );
    return $config;
});

if ($app['debug'] === true) {
    $app->register(new WebProfilerServiceProvider());
}

$app->error(function (\Exception $e, $code) use ($app) {
    if ($app['debug'] === true) {
        return;
    }

    $message = 'Oops, something went wrong';

    if ($e instanceof HttpException) {
        $message = $e->getMessage();
    }

    return new MessageResponse($message, array(), $code, array(
        'Content-Type' => 'application/xml; charset=UTF-8'
    ));
});

$app->register(new ConfigServiceProvider(
    sprintf('%s/app/config/%s.json', $app['app.rootDir'], APP_ENV),
    array('app.rootDir' => $app['app.rootDir'])
));

return $app;