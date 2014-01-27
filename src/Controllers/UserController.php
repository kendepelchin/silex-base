<?php

namespace Controllers;

use Silex\Application;
use Silex\ControllerCollection;
use Silex\ControllerProviderInterface;
use Symfony\Component\HttpFoundation\Request;

use Controllers\CoreController;
use Classes\Form\User\RegisterForm;
use Classes\User\Helper as userHelper;
use Classes\User\Repository;
use Classes\User\User;

/**
 * User Controller
 *
 * @extends Controllers\CoreController
 * @author  Ken Depelchin <ken.depelchin@gmail.com>
 */
class UserController extends CoreController {

    public function init(Request $request) {}

    protected function getRoutes(ControllerCollection $controllers) {
        $controllers->get('/register', array($this, 'register'))->method('POST');
        $controllers->get('/register', array($this, 'register'))->bind('register');
        $controllers->get('/login', array($this, 'login'))->bind('login');
        $controllers->get('/logout', array($this, 'logout'))->bind('logout');

        return $controllers;
    }

    public function register(Request $request) {
        $form = new RegisterForm($this->getFormFactory());
        if ('POST' == $request->getMethod()) {
            $form->bind($request);

            if ($form->isValid()) {
                $data = $form->getData();
                $user = array();
                $user['username'] = $data['username'];
                $user['email'] = $data['email'];
                $user['dateadd'] = date('Y-m-d H:i:s');
                $user['roles'] = 'ROLE_USER';
                $user['salt'] = UserHelper::generateSalt($data['username']);
                $user['password'] = $this->app['security.encoder.digest']->encodePassword($data['password'], $user['salt']);

                $exists = $this->app['users']->findUser($data['username'], $data['email']);

                if (!$exists) {
                    $result = $this->app['users']->insert($user);

                    if ($result) {
                        $userId = $this->app['users']->getUserId($user['username']);
                        $user = new User($userId, $user['username'], $user['email'], $user['password'], $user['salt'], array('ROLE_USER'));

                        $this->app['security']->setToken(new \Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken($user, $user->getPassword(), 'admin', array('ROLE_USER')));

                        return $this->app->redirect('/');
                    }
                    echo 'user created!';
                }

                else {
                    echo 'user already exists.';
                }

                // redirect somewhere
                return $this->app->redirect('login');
            }
        }

        // display the form
        return $this->render('user/register.twig', array(
            'form' => $form->getForm()->createView())
        );
    }

    public function login(Request $request) {
        return $this->getTwig()->render('user/login.twig', array(
            'error'         => $this->app['security.last_error']($request),
            'last_username' => $this->app['session']->get('_security.last_username'),
            'redirect_url'  => $request->get('redirect')
        ));
    }

    public function logout(Request $request) {
        return $this->getTwig()->render('home/logout.twig', array());
    }
}
