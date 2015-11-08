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
        foreach ($instances as $key => $value) {
            $instances[$key] = $this->prepareConfigInstance($value);
        }
        return $instances;
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