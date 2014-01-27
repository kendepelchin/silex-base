<?php

namespace Classes\User;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\Encoder\PasswordEncoderInterface;
use Symfony\Component\Validator\Mapping\ClassMetadata;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\ExecutionContext;

/**
 * Our User Entity class
 *
 * @implements Symfony\Component\Security\Core\User\UserInterface
 * @author  Ken Depelchin <ken.depelchin@gmail.com>
 */
class User implements UserInterface {

    private $id;
    private $username;
    private $email;
    private $password;
    private $salt;
    private $enabled;
    private $roles;
    private $services;
    private $settings;

    public function __construct($id, $username, $email, $password, $salt, array $roles = array(), $services = array(), $enabled = true) {
        if (empty($username)) {
            throw new \InvalidArgumentException('The username cannot be empty.');
        }

        $this->id = $id;
        $this->username = $username;
        $this->email = $email;
        $this->password = $password;
        $this->salt = $salt;
        $this->enabled = $enabled;
        $this->roles = $roles;
        $this->services = $services;
    }

    /**
     * {@inheritdoc}
     */
    public function eraseCredentials() {

    }

    /**
     * {@inheritdoc}
     */
    public function getId() {
        return $this->id;
    }

    /**
     * {@inheritdoc}
     */
    public function getPassword() {
        return $this->password;
    }

    /**
     * {@inheritdoc}
     */
    public function getRoles() {
        return $this->roles;
    }

    /**
     * {@inheritdoc}
     */
    public function getSalt() {
        return $this->salt;
    }

    /**
     * Get services for the current User (use this for using serviceids)
     *
     * @param   string      $key[optional]      use this to return only a specific part
     * @return  array                           all connected services for this user
     */
    public function getServices($key = null) {
        if ($key && isset($this->services[$key])) {
            return $this->services[$key];
        }

        return $this->services;
    }

    /**
     * Returns the service id for the specific type of service connected to this user
     *
     * @param   string      $key        the type of service
     * @return  string                  returns the service id
     */
    public function getServiceId($key) {
        if (!isset($this->services[$key])) {
            return false;
        }

        return $this->services[$key]['service_id'];
    }

    /**
     * {@inheritdoc}
     */
    public function getUsername() {
        return $this->username;
    }

    /**
     * {@inheritdoc}
     */
    public function getEmail() {
        return $this->email;
    }

    /**
     * {@inheritdoc}
     */
    public function isEnabled() {
        return $this->enabled;
    }
}
