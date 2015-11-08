<?php
if (!function_exists('prepareConfigInstances')) {
    /**
     * Prepare an array of instantiable configuration instances.
     *
     * @author Morten Rugaard <moru@nodes.dk>
     *
     * @param  array $instances
     * @return array
     */
    function prepareConfigInstances(array $instances)
    {
        foreach ($instances as $key => $value) {
            $instances[$key] = $this->prepareConfigInstance($value);
        }
        return $instances;
    }
}

if (!function_exists('prepareConfigInstance')) {
    /**
     * Prepare an instantiable configuration instance.
     *
     * @author Morten Rugaard <moru@nodes.dk>
     *
     * @param  mixed $instance
     * @return object
     */
    function prepareConfigInstance($instance)
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