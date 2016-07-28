<?php

namespace Nodes\Support\UserAgent\Agents;

/**
 * Class Nodes.
 */
class Nodes
{
    /**
     * User agent.
     *
     * @var string
     */
    protected $userAgent;

    /**
     * Version number.
     *
     * @var string
     */
    protected $version;

    /**
     * Major version number.
     *
     * @var int
     */
    protected $majorVersion;

    /**
     * Minor version number.
     *
     * @var int
     */
    protected $minorVersion;

    /**
     * Patch version number.
     *
     * @var string
     */
    protected $patchVersion;

    /**
     * Debug mode.
     *
     * @var bool
     */
    protected $debug;

    /**
     * Platfrom name and verison.
     *
     * @var string
     */
    protected $platform;

    /**
     * Device name.
     *
     * @var string
     */
    protected $device;

    /**
     * Indicator if parsing was successful or not.
     *
     * @var bool
     */
    protected $successful = false;

    /**
     * Nodes constructor.
     *
     * @author Morten Rugaard <moru@nodes.dk>
     *
     * @param  string  $userAgent
     */
    public function __construct($userAgent)
    {
        // Set user agent string
        $this->userAgent = $userAgent;

        // Parse user agent string
        $this->parse();
    }

    /**
     * Parse user agent string.
     *
     * @author Morten Rugaard <moru@nodes.dk>
     *
     * @return void
     */
    protected function parse()
    {
        // Parse Nodes user agent
        if (! preg_match('|Nodes/(.*)\s\((.*)\)|s', $this->userAgent, $match)) {
            return;
        }

        // Put matches into their own variables
        list($version, $parameters) = array_slice($match, 1);

        // Split comma-separated parameters
        $parameters = explode(',', $parameters);

        // Set version if available
        if (! empty($version)) {
            $this->setVersion($version);
        }

        // Set parameters if available
        if (! empty($parameters)) {
            $this->setParameters($parameters);
        }

        // Mark parsing as successful
        $this->successful = true;
    }

    /**
     * Set version.
     *
     * @author Morten Rugaard <moru@nodes.dk>
     *
     * @param  string $version
     * @return $this
     */
    public function setVersion($version)
    {
        // Set version
        $this->version = $version;

        // Split version into major, minor and patch
        $version = explode('.', $version);
        if (count($version) == 1) {
            array_push($version, 0, 0);
        }

        // Set major, minor and patch version
        $this->majorVersion = (int) $version[0];
        $this->minorVersion = (int) $version[1];
        $this->patchVersion = (int) $version[2];

        return $this;
    }

    /**
     * Set parameters.
     *
     * @author Morten Rugaard <moru@nodes.dk>
     *
     * @param  array $parameters
     * @return $this
     */
    public function setParameters(array $parameters)
    {
        foreach ($parameters as $iteration => $value) {
            // Trim whitespaces
            $value = trim($value);

            switch ($iteration) {
                case 0: // Debug mode
                    $this->debug = ($value == 'debug') ? true : false;
                    break;
                case 1: // Platform name and version
                    $this->platform = $value;
                    break;
                case 2: // Device name
                    $this->device = $value;
                    break;
            }
        }

        return $this;
    }

    /**
     * Retrieve user agent string.
     *
     * @author Morten Rugaard <moru@nodes.dk>
     *
     * @return string
     */
    public function getUserAgent()
    {
        return $this->userAgent;
    }

    /**
     * Retrieve version number.
     *
     * @author Morten Rugaard <moru@nodes.dk>
     *
     * @return string
     */
    public function getVersion()
    {
        return $this->version;
    }

    /**
     * Retrieve major version number.
     *
     * @author Morten Rugaard <moru@nodes.dk>
     *
     * @return int
     */
    public function getMajorVersion()
    {
        return $this->majorVersion;
    }

    /**
     * Retrieve minor version number.
     *
     * @author Morten Rugaard <moru@nodes.dk>
     *
     * @return int
     */
    public function getMinorVersion()
    {
        return $this->minorVersion;
    }

    /**
     * Retrieve patch version number.
     *
     * @author Morten Rugaard <moru@nodes.dk>
     *
     * @return int
     */
    public function getPatchVersion()
    {
        return $this->patchVersion;
    }

    /**
     * Retrieve debug mode.
     *
     * @author Morten Rugaard <moru@nodes.dk>
     *
     * @return bool
     */
    public function getDebug()
    {
        return $this->debug;
    }

    /**
     * Retrieve platform name and version.
     *
     * @author Morten Rugaard <moru@nodes.dk>
     *
     * @return string
     */
    public function getPlatform()
    {
        return $this->platform;
    }

    /**
     * Retrieve device name.
     *
     * @author Morten Rugaard <moru@nodes.dk>
     *
     * @return string
     */
    public function getDevice()
    {
        return $this->device;
    }

    /**
     * Check if parsing of user agent was successful or not.
     *
     * @author Morten Rugaard <moru@nodes.dk>
     *
     * @return bool
     */
    public function success()
    {
        return $this->successful;
    }

    /**
     * toArray.
     *
     * @author Casper Rasmussen <cr@nodes.dk>
     *
     * @return array
     */
    public function toArray()
    {
        return [
            'userAgent' => $this->userAgent,
            'version' => $this->version,
            'majorVersion' => $this->majorVersion,
            'minorVersion' => $this->minorVersion,
            'patchVersion' => $this->patchVersion,
            'debug' => $this->debug,
            'platform' => $this->platform,
            'device' => $this->device,
        ];
    }
}
