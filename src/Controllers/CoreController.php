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
        $this->app['twig']->addGlobal('userId', $this->getUserId());

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
     * Build urls for redirect purposes
     *
     * @param   string $path The path to generate an url for (mount)
     * @param   array  $params params to add to the url
     * @param   array  $query http query
     * @return  string
     */
    protected function buildUrl($path, $params = array(), $query = array()) {
        $url = $this->getUrl()->generate($path, $params);
        if (!empty($query)) {
            $url .= '?' . http_build_query($query);
        }

        return $this->app->redirect($url);
    }

    /**
     * Method to get a user
     *
     * @return User|Null|string
     */
    protected function getUser() {
        if(is_null($this->getSecurity()->getToken())) {
            return null;
        }

        return $this->getSecurity()->getToken()->getUser();
    }

    /**
     * Method to return the authed userId
     *
     * @return  int|null
     */
    protected function getUserId() {
        if (!$this->isGranted('ROLE_USER')) {
            return null;
        }

        if (is_null($this->getUser())) {
            return null;
        }

        return (int) $this->getUser()->getId();
    }

    /**
     * Method to check if role is granted
     *
     * @param   string $role The role to check
     * @return  boolean
     */
    protected function isGranted($role) {
        if (in_array($role, array('ROLE_USER', 'ROLE_ADMIN'))) {
            return $this->app['security']->isGranted($role);
        }
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
