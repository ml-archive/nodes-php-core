<?php
if (!function_exists('loadDirectory')) {
    function loadFilesRecursively($path) {
        if (is_file($path)) {
            require_once($path);
            return true;
        }

        // Scan directory
        $directory = scandir($path);
        $directory = array_slice($directory, 2);

        // Parse directory
        foreach ($directory as $item) {
            if (is_dir($path . $item)) {
                // Load directory
                self::loadFilesRecursively($path . $item . '/');
            } else {
                // Load file
                require_once($path . $item);
            }
        }

        return true;
    }
}