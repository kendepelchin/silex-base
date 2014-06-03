<?php

// Composer autoloading (PSR-0).
require_once __DIR__ . '/../vendor/autoload.php';

// Require config.
require_once __DIR__ . '/config.php';

// Namespaces.
use Knp\Provider\RepositoryServiceProvider;
use Igorw\Silex\ConfigServiceProvider;
use Silex\Provider\DoctrineServiceProvider;
use Silex\Provider\FormServiceProvider;
use Silex\Provider\MonologServiceProvider;
use Silex\Provider\SecurityServiceProvider;
use Silex\Provider\SessionServiceProvider;
use Silex\Provider\TranslationServiceProvider;
use Silex\Provider\TwigServiceProvider;
use Silex\Provider\UrlGeneratorServiceProvider;
use Silex\Provider\ValidatorServiceProvider;
use Symfony\Component\Security\Core\Encoder\MessageDigestPasswordEncoder;
use Knp\Provider\ConsoleServiceProvider;

/**
 * Application Configuration
 *
 */
$app = new Silex\Application();
$app['debug'] = true;

/**
 * ConfigServiceProvider
 *
 */
$app->register(new ConfigServiceProvider(__DIR__ . '/config.php'));

/**
 * Doctrine - Database Access
 *
 */
$app->register(new DoctrineServiceProvider(), array(
    'db.options' => array(
        'driver' => 'pdo_mysql',
        'dbname' => 'dbname', // @todo
        'host' => '', // @todo
        'user' => '', // @todo
        'password' => '', // @todo
        'charset' => 'utf8',
    ),
));

/**
 * Form Service Provider
 *
 */
$app->register(new FormServiceProvider());

/**
 * MONOLOG - For logging
 *
 */
$app->register(new MonologServiceProvider(), array(
    // 'monolog.logfile' => __DIR__ . '/../logs/' . date('Y:m:d') . 'development.log',
    'monolog.name' => 'vinyl',
));

/**
 * Repository Service Provider
 * https://github.com/KnpLabs/RepositoryServiceProvider
 *
 */
$app->register(new RepositoryServiceProvider(), array(
        'repository.repositories' => array(
            // @todo
        )
));

/**
 * Security user provider
 *
 */
$app->register(new SecurityServiceProvider(), array(
    'security.firewalls' =>  array(
        'admin' => array(
            'anonymous'=> array(),
            'pattern' => '^/',
            'http' => true,
            'form' => array('login_path' => '/user/login', 'check_path' => '/admin/login_check'),
            'logout' => array('logout_path' => '/user/logout'),
            'users' => $app->share(function() use ($app){
                return new Classes\User\UserProvider($app['db']);
            })
        ),
    ),
));

/**
 * Session
 *
 */
$app->register(new SessionServiceProvider());

/**
 * Translation Service Provider (needed for form & validator)
 *
 */
$app->register(new TranslationServiceProvider(), array(
    'locale_fallback' => 'en',
));

/**
 * Twig - Template Engine.
 *
 */
$app->register(new TwigServiceProvider(), array(
    'twig.path' => __DIR__ . '/../web/public/views'
));

// Extending Twig
$app['twig'] = $app->extend("twig", function (\Twig_Environment $twig, Silex\Application $app) {
    $twig->addExtension(new Classes\Utils\Filters($app));

    return $twig;
});

/**
 * Shorthands for paths & urls
 *
 */
$app->register(new UrlGeneratorServiceProvider());

/**
 * Validator Service Provider
 *
 */
$app->register(new ValidatorServiceProvider());

/**
 * Console Service Provider
 *
 */
$app->register(new ConsoleServiceProvider(), array(
    'console.name'              => 'ConsoleApp',
    'console.version'           => '1.0.0',
    'console.project_directory' => __DIR__ . '/..'
));

return $app;
