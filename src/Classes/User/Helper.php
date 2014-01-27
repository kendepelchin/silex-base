<?php

namespace Classes\User;

/**
 * Class that stores helper methods for users
 *
 * @author  Ken Depelchin <ken.depelchin@gmail.com>
 */
class Helper {

    const SALT_LENGTH = 20;

    /**
     * Method to generate a random salt with the user email address
     *
     * @param   string      $email      The User's email
     * @return  string
     */
    public static function generateSalt($email) {
        $ret = '';
        $letters = range('a', 'z');
        $symbols = range('.', '%');
        $symbols = array_merge($letters, $symbols);
        shuffle($symbols);

        while (strlen($ret) < self::SALT_LENGTH) {
            $ret .= $symbols[rand(0, count($symbols) - 1)];
        }

        return $email . $ret;
    }

    public static function generateGravatarHash($email) {
        return md5(strtolower(trim($email)));
    }

    public static function getGravatarImage($email, $size = 50) {
        // @todo add own default image
        return "http://www.gravatar.com/avatar/" . self::generateGravatarHash($email) . '?s=' . $size;
    }
}
