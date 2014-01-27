<?php

namespace Controllers;

use Silex\ControllerCollection;
use Symfony\Component\HttpFoundation\Request;

use Controllers\CoreController;

/**
 * Home Controller
 *
 * @extends Controllers\CoreController
 * @author  Ken Depelchin <ken.depelchin@gmail.com>
 */
class HomeController extends CoreController {

    public function init(Request $request) {}

    protected function getRoutes(ControllerCollection $controllers) {
        $controllers->get('/', array($this, 'index'))->before(array($this, 'init'));
        $controllers->get('/', array($this, 'index'))->bind('home');
        $controllers->get('/login', array($this, 'login'))->bind('login');

        return $controllers;
    }

    public function index(Request $request) {
        if ($this->app['security']->isGranted('ROLE_USER')) {
            // logged in
        }
        return $this->getTwig()->render(
            'home/index.twig', array(
            )
        );
    }
}
