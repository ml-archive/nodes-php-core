<?php
if (!function_exists('addTrailingSlash')) {
    /**
     * Add trailing slash to string if missing.
     *
     * @author Morten Rugaard <moru@nodes.dk>
     *
     * @param  string $string
     * @return string
     */
    function addTrailingSlash($string)
    {
        if (substr($string, -1) != '/') {
            $string .= '/';
        }

        return $string;
    }
}

if (!function_exists('removeTrailingSlash')) {
    /**
     * Remove trailing slash from string if present.
     *
     * @author Morten Rugaard <moru@nodes.dk>
     *
     * @param  string $string
     * @return string
     */
    function removeTrailingSlash($string)
    {
        if (substr($string, -1) == '/') {
            $string = substr($string, 0, strlen($string) - 1);
        }

        return $string;
    }
}