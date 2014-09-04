<?php

namespace Classes\Utils;

/**
 * Class to handle files and directories
 *
 * @author Ken Depelchin <ken.depelchin@gmail.com>
 */
class File {

    public static function readDirectory($path) {
        $files = array();

        if (is_dir($path)) {
            $handle = opendir($path);
            if ($handle) {
                while (($file = readdir($handle)) !== false) {
                    if ($file == '.' || $file == '..') {
                        continue;
                    }

                    if (is_file($path . '/' . $file)) {
                        $files[] = $file;
                    }
                }

                // close the directory handle
                closedir($handle);
            }
        }

        return $files;
    }
}
