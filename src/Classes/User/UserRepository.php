<?php

namespace Classes\User;

use Knp\Repository;

/**
 * Our User model to handle our user database entity
 *
 * @extends Knp\Repository
 * @author  Ken Depelchin <ken.depelchin@gmail.com>
 */
class UserRepository extends Repository {

    public function getTableName() {
        return 'users';
    }

    public function findUser($username, $email) {
        return $this->db->fetchColumn('SELECT u.id FROM users AS u WHERE u.username = ? OR u.email = ?', array($username, $email));
    }

    public function getUserId($username) {
        return $this->db->fetchColumn('SELECT u.id FROM users AS u WHERE u.username = ?', array($username));
    }
}
