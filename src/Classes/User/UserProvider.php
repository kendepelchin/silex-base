<?php

namespace Classes\User;

use Doctrine\DBAL\Connection;
use Doctrine\ORM\EntityManager;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;

use Classes\User\User;

/**
 * This is our UserProvider. It is used for your SecurityProvider
 *
 * @implements Symfony\Component\Security\Core\User\UserProviderInterface
 * @author  Ken Depelchin <ken.depelchin@gmail.com>
 */
class UserProvider implements UserProviderInterface {

    private $conn;

    public function __construct(Connection $conn) {
        $this->conn = $conn;
    }

    /**
     * Method used for logging users in and returning our User Entity object
     *
     * @param   string      $username     The email
     * @return  Classes\User\User
     */
    public function loadUserByUsername($username) {
        $stmt = $this->conn->executeQuery('SELECT * FROM users WHERE username = ? OR email = ?', array(strtolower($username), strtolower($username)));

        if (!$user = $stmt->fetch()) {
            throw new UsernameNotFoundException(sprintf('Username "%s" does not exist.', $username));
        }

        $services = $this->getServicesForUser($user['id']);
        return new User($user['id'], $user['username'], $user['email'], $user['password'], $user['salt'], explode(',', $user['roles']), $services);
    }

    /**
     * Method to fetch all connected services for a user
     *
     * @param   int     $userId     The user id
     * @return  array
     */
    public function getServicesForUser($userId) {
        $ret = array();
        $services = $this->conn->fetchAll('SELECT s.* FROM services AS s WHERE s.user_id = ?', array($userId));
        if (is_array($services) && !empty($services)) {
            foreach ($services as $service) {
                $ret[$service['type']] = $service;
            }
        }

        return $ret;
    }

    public function refreshUser(UserInterface $user) {
        if (!$user instanceof User) {
            throw new UnsupportedUserException(sprintf('Instances of "%s" are not supported.', get_class($user)));
        }

        return $this->loadUserByUsername($user->getUsername());
    }

    public function supportsClass($class) {
        return $class === 'Symfony\Component\Security\Core\User\User';
    }
}
