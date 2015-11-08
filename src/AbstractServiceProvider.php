<?php
/**
 * This service provider should be extended by all Nodes Packages.
 */
namespace Nodes;

use Illuminate\Support\ServiceProvider as IlluminateServiceProvider;
use Nodes\Exception\HelperDirectoryNotFoundException;

/**
 * Class ServiceProvider
 *
 * @abstract
 * @package Nodes\Core
 */
abstract class AbstractServiceProvider extends IlluminateServiceProvider
{
    /**
     * Load files with helper methods
     *
     * @author Morten Rugaard <moru@nodes.dk>
     *
     * @access public
     * @param  string  $path      Path to directory (must contain trailing slash)
     * @param  boolean $recursive Load recursively
     * @return boolean
     * @throws \Nodes\Exception\HelperDirectoryNotFoundException
     */
    public function loadHelpers($path = null, $recursive = true)
    {
        // If no path is provided,
        // we'll assume path is ./Support/Helpers
        if (empty($path)) {
            $path = __DIR__ . '/Support/Helpers/';
        }

        // Make sure directory exists
        if (!file_exists($path)) {
            throw new HelperDirectoryNotFoundException(sprintf('Could not register helpers. Reason: Helper directory does not exist (%s)', $path));
        }

        // Scan directory
        $directory = scandir($path);
        $directory = array_slice($directory, 2);

        // No files found in directory
        if (empty($directory)) {
            return false;
        }

        foreach ($directory as $item) {
            // If item is a directory and recursive is false.
            // We'll simply skip the directory and move on.
            if (is_dir($path . $item) && !$recursive) {
                continue;
            }

            if (is_dir($path . $item)) {
                // Load directory
                $this->loadHelpers($path . $item . '/');
            } else {
                // Load file
                include_once ($path . $item);
            }
        }

        return true;
    }
}