<?php
if (!function_exists('load_directory')) {
    /**
     * Load all files in directory
     *
     * @author Morten Rugaard <moru@nodes.dk>
     *
     * @access public
     * @param  string  $path      Path to directory (must contain trailing slash)
     * @param  boolean $recursive Load recursively
     * @return boolean
     */
    function load_directory($path, $recursive = true) {
        // Make sure directory exists
        if (!file_exists($path)) {
            return false;
        }

        // Scan directory
        $directory = scandir($path);
        $directory = array_slice($directory, 2);

        // No files found in directory
        if (empty($directory)) {
            return false;
        }

        // Parse directory
        foreach ($directory as $item) {
            // If item is a directory and recursive is false.
            // We'll simply skip the directory and move on.
            if (is_dir($path . $item) && !$recursive) {
                continue;
            }

            if (is_dir($path . $item)) {
                // Load directory
                load_directory($path . $item . '/');
            } else {
                // Load file
                include ($path . $item);
            }
        }

        return true;
    }
}