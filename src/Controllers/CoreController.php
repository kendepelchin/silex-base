<?php

namespace Controllers;

use Doctrine\ORM\EntityManager;
use Silex\Application;
use Silex\ControllerProviderInterface;
use Symfony\Component\Form\FormFactory;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\HttpKernel;
use Symfony\Component\Routing\Generator\UrlGenerator;
use Symfony\Component\Security\Core\SecurityContext;
use Symfony\Component\Translation\Translator;

/**
 * Core Controller
 *
 * @implements Silex\ControllerProviderInterface
 * @author  Ken Depelchin <ken.depelchin@gmail.com>
 */
abstract class CoreController implements ControllerProviderInterface {

    /*
     *  Silex\Application
     */
    public $app;
    public $logger;
    public $session;

    public function connect(Application $app) {
        $this->app = $app;
        $this->logger = $app['logger'];
        $this->session = $app['session'];
        return $this->getRoutes($this->getControllerFactory());
    }

    /**
     * @return ControllerCollection
     */
    protected function getControllerFactory() {
        return $this->app['controllers_factory'];
    }

    /**
     * @return HttpKernel
     */
    protected function getKernel() {
        return $this->app['kernel'];
    }

    /**
     * @return Request
     */
    protected function getRequest() {
        return $this->app['request'];
    }

    /**
     * @return FormFactory
     */
    protected function getFormFactory() {
        return $this->app['form.factory'];
    }

    /**
     * @return UrlGenerator
     */
    protected function getUrlGenerator() {
        return $this->app['url_generator'];
    }

    /**
     * @return \Twig_Environment
     */
    protected function getTwig() {
        // add globals
        $this->app['twig']->addGlobal('user', $this->getUser());
        return $this->app['twig'];
    }

    /**
     * @return EntityManager
     */
    protected function getEntityManager() {
        return $this->app['orm.em'];
    }

    /**
     * @return Translator
     */
    protected function getTranslator() {
        return $this->app['translator'];
    }

    /**
     * @return SecurityContext
     */
    protected function getSecurity() {
        return $this->app['security'];
    }

    protected function getEncoderFactory() {
        return $this->app['security.encoder_factory'];
    }

    protected function getLogger() {
        return $this->logger;
    }

    protected function getDatabase() {
        return $this->app['db'];
    }

     /**
     * Get config which is registers in the Config Service Provider
     *
     * @param   string       $key      the key to get
     * @return  array
     */
    protected function getServiceConfig($key) {
        if (isset($this->app['_services'][$key])) {
            return $this->app['_services'][$key];
        }

        return false;
    }

    /**
     * @return User|Null|string
     */
    protected function getUser() {
        if(is_null($this->getSecurity()->getToken())) {
            return null;
        }

        return $this->getSecurity()->getToken()->getUser();
    }

    /**
     * @param $view
     * @param array $parameters
     * @return string
     */
    protected function render($view, array $parameters = array()) {
        return $this->getTwig()->render($view, $parameters);
    }
}
