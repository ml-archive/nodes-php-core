<?php
if (!function_exists('prepare_config_instances')) {
    /**
     * Prepare an array of instantiable configuration instances.
     *
     * @author Morten Rugaard <moru@nodes.dk>
     *
     * @param  array $instances
     * @return array
     */
    function prepare_config_instances(array $instances)
    {
        // Loaded instances
        $loadedInstances = [];

        foreach ($instances as $key => $value) {
            $loadedInstances[$key] = prepare_config_instance($value);
        }

        return $loadedInstances;
    }
}

if (!function_exists('prepare_config_instance')) {
    /**
     * Prepare an instantiable configuration instance.
     *
     * @author Morten Rugaard <moru@nodes.dk>
     *
     * @param  mixed $instance
     * @return object
     */
    function prepare_config_instance($instance)
    {
        if (is_callable($instance)) {
            return call_user_func($instance, app());
        } elseif (is_string($instance)) {
            return app($instance);
        } else {
            return $instance;
        }
    }
}

if (!function_exists('add_to_autoload_config')) {
    /**
     * Add path to Nodes autoload config
     *
     * @author Morten Rugaard <moru@nodes.dk>
     *
     * @param  mixed $paths
     * @return bool
     */
    function add_to_autoload_config($paths)
    {
        // Retrieve full path of autoload config
        $autoloadPath = config_path('nodes/autoload.php');

        // Make sure autoload config exists
        if (!file_exists($autoloadPath)) {
            return false;
        }

        // Load autoload config into an array of each line
        $autoloadConfig = file($autoloadPath);

        // Determine line in config file to start adding from
        $lookForStartingPosition = array_keys(preg_grep('|// Paths should be relative to root folder|i', $autoloadConfig));
        $startPosition = !empty($lookForStartingPosition[0]) ? (int) $lookForStartingPosition[0]+1 : 2;

        // Make paths is always an array
        $paths = !is_array($paths) ? [$paths] : $paths;

        foreach ($paths as $path) {
            // Wrap path in single quotes
            $path = sprintf("'%s'", $path);

            // If path already exists in file,
            // we'll skip it and move along
            if (preg_grep(sprintf('|%s|i', $path), $autoloadConfig)) {
                continue;
            }

            // Loop through each line of file to find the correct
            // position to insert current path
            for ($i = $startPosition; $i < count($autoloadConfig); $i++) {
                // Retrieve value of next line
                $value = trim($autoloadConfig[$i]);

                // Current line is not the right place for this path.
                // Move on to the next one.
                if ($value != '];' && strnatcmp($value, $path) < 0) {
                    continue;
                }

                // Insert path at current line
                array_splice($autoloadConfig, $i, 0, [
                    str_repeat(' ', 4) . $path . ",\n"
                ]);
                break;
            }
        }

        // Save autoload config with new paths
        file_put_contents($autoloadPath, $autoloadConfig);

        return true;
    }
}

if (!function_exists('add_to_composer_autoload')) {
    /**
     * Add key and/or value to Composer's autoload
     *
     * @author Morten Rugaard <moru@nodes.dk>
     *
     * @param  string $section Section in Composer's autoload (i.e. classmap)
     * @param  string $value   Value to add to $section
     * @param  string $key     Key to pair with $value in $section
     * @return void
     */
    function add_to_composer_autoload($section, $value, $key = null)
    {
        // File path to composer file
        $composerFilePath = base_path('composer.json');

        // Load and JSON decode composer file
        $composerFile = json_decode(file_get_contents($composerFilePath));

        // Make sure value doesn't already exists
        if ((!is_null($key) && array_key_exists($key, $composerFile->autoload->{$section})) ||
            in_array($value, $composerFile->autoload->{$section})) {
            return;
        }

        // Add to composer's {section}
        if (!is_null($key)) {
            $composerFile->autoload->{$section}[$key] = $value;
        } else {
            $composerFile->autoload->{$section}[] = $value;
        }

        // Save changes to composer file
        file_put_contents($composerFilePath, json_encode($composerFile, JSON_PRETTY_PRINT));
    }
}