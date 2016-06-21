<?php

namespace bublak\phpunitmultirunner\Tree\Helpers;

class File {

    public static function isPhpFile($name) {
        $result = false;

        $pos = strpos($name, '.php');

        if ($pos !== false && strlen($name) - $pos == 4) {
            $result = true;
        }

        return $result;
    }

    /**
     * Load files from folder (only one level).
     *
     * @param string $folder absolute path to folder, where search files
     *
     * @return array with file names without path in requested folder
     */
    public static function getDirContent($folder) {
        $result = false;

        if (empty($folder)) {
            return $result;
        }

        $result = array_diff(scandir($folder), array('..', '.'));

        if (count($result) == 0) {
            $result = false;
        } else {
            $result = array_values($result);
        }

        return $result;
    }
}
