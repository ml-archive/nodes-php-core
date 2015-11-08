<?php
if (!function_exists('headers')) {
    /**
     * Retrieve one or more headers from request.
     *
     * Note: Returns all headers, if no keys array is provided.
     *
     * @author Morten Rugaard <moru@nodes.dk>
     *
     * @param  array $keys
     * @return array
     */
    function headers(array $keys = [])
    {
        // Retrieve all request headers
        $requestHeaders = \Request::header();

        // Convert all values to lowercase
        $keys = array_map('strtolower', $keys);

        // Found headers container
        $headers = [];

        foreach ($requestHeaders as $requestHeader => $value) {
            if (!empty($keys) && !in_array($requestHeader, $keys)) {
                continue;
            }

            // Add to headers container
            $headers[$requestHeader] = implode(';', $value);
        }

        return $headers;
    }
}