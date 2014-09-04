<?php

namespace Classes\Console;

use Classes\Utils\File;

/**
 * Console class with an overview of all our scripts that can be runned
 *
 * @author Ken Depelchin <ken.depelchin@gmail.com>
 */
class Console {

    // Default interval, can be override in $config
    const DEFAULT_INTERVAL = 60;

    // Scripts
    const PLACEHOLDER = 'placeholder';

    // This is our base script which runs all other scripts (skip this)
    const CONSOLE = 'console';

    protected static $skip = array(
        self::CONSOLE
    );

    protected static $config = array(
        self::PLACEHOLDER => array(
            'name' => 'start',
            'description' => 'Add your description here.',
            'help' => '',
            'interval' => '10', // seconds
        ),
    );

    /**
     * Is a certain script a valid executable script?
     *
     * @param   string $script The script to check
     * @return  boolean
     */
    public static function isValidScript($script) {
        return in_array($script, array_keys(self::$config)) && !in_array($script, self::$skip);
    }

    /**
     * Get config for a script
     *
     * @param   string $script The script
     * @return  array
     */
    public static function getConfig($script) {
        if (self::isValidScript($script)) {
            return self::$config[$script];
        }
        else {
            return false;
        }
    }

    /**
     * Lock a script file
     *
     * @param   string $script The script to lock
     * @return  boolean
     */
    public static function lock($script) {
        $path = self::getLockForScript($script);
        $lock = fopen($path, 'c+');

        if (flock($lock, LOCK_EX)) {
            fwrite($lock, getmypid());
            return true;
        }

        fclose($lock);
        return false;
    }

    /**
     * fetch locks and kill the scripts
     *
     * @param   string $script The script to kill
     * @return  boolean
     */
    public static function kill($script) {
        $path = self::getLockForScript($script);
        if (!file_exists($path)) {
            return false;
        }

        if (!self::isRunning($script)) {
            return false;
        }

        if ($lock = fopen($path, 'c+')) {
            $killCmd = "pgrep -f 'app/console/$script' | xargs kill";
            shell_exec($killCmd);

            fclose($lock);

            // will return true on success, false on failure
            return unlink($path);
        }
    }

    /**
     * Is a certain script already running?
     *
     * @param   string $script The script to check
     * @return  boolean
     */
    public static function isRunning($script) {
        if (file_exists(self::getLockForScript($script))) {
            return true;
        }

        return false;
    }

    /**
     * Run a script (with all the necessary checks)
     *
     * @param   string $script The script to run
     * @return  boolean
     */
    public static function run($script) {
        $config = self::getConfig($script);

        if ($config) {
            // check if script is running (or are we in debug mode?)
            if (!Console::isRunning($script)) {
                $type = $config['name'];
                $logDir = __DIR__ . "/../../../logs/console/$script";

                // lock the script
                self::lock($script);

                // & => os run in background
                $cmd = "nohup app/console/$script $type > $logDir &";

                // run the script
                shell_exec($cmd);
                return true;
            }
            else {
                return false;
            }
        }
        else {
            return false;
        }
    }

    /**
     * Return the full path for a script lock file
     *
     * @param   string $script The script
     * @return  string
     */
    protected static function getLockForScript($script) {
        return __DIR__ . '/../../../logs/locks/' . "$script.pid";
    }
}
