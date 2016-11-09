<?php

if (!function_exists('add_trailing_slash')) {
    /**
     * Add trailing slash to string if missing.
     *
     * @author Morten Rugaard <moru@nodes.dk>
     *
     * @param string $string
     *
     * @return string
     */
    function add_trailing_slash($string)
    {
        if (substr($string, -1) != '/') {
            $string .= '/';
        }

        return $string;
    }
}

if (!function_exists('remove_trailing_slash')) {
    /**
     * Remove trailing slash from string if present.
     *
     * @author Morten Rugaard <moru@nodes.dk>
     *
     * @param string $string
     *
     * @return string
     */
    function remove_trailing_slash($string)
    {
        if (substr($string, -1) == '/') {
            $string = substr($string, 0, strlen($string) - 1);
        }

        return $string;
    }
}
