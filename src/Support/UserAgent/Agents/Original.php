<?php
namespace Nodes\Support\UserAgent\Agents;
use phpbrowscap\Browscap;

/**
 * Class Original
 *
 * @package Nodes\Support\UserAgent\Agents
 */
class Original
{
    /**
     * User agent
     *
     * @var string
     */
    protected $userAgent;

    /**
     * Browser name
     *
     * @var string
     */
    protected $browser;

    /**
     * Browser version
     *
     * @var integer
     */
    protected $version = 0;

    /**
     * Browser major version
     *
     * @var integer
     */
    protected $majorVersion = 0;

    /**
     * Browser minor version
     *
     * @var integer
     */
    protected $minorVersion = 0;

    /**
     * Browser with version
     *
     * @var string
     */
    protected $browserWithVersion;

    /**
     * Browser publisher
     *
     * @var string
     */
    protected $publisher;

    /**
     * Platform name
     *
     * @var string
     */
    protected $platform;

    /**
     * Device name
     *
     * @var string
     */
    protected $device;

    /**
     * Device pointing method
     *
     * @var string
     */
    protected $devicePointer;

    /**
     * Did request come from a mobile
     *
     * @var boolean
     */
    protected $isMobile = false;

    /**
     * Did request come from a tablet
     *
     * @var boolean
     */
    protected $isTablet = false;

    /**
     * Did request come from a crawler
     *
     * @var boolean
     */
    protected $isCrawler = false;

    /**
     * Original constructor
     *
     * @author Morten Rugaard <moru@nodes.dk>
     *
     * @access public
     * @param  string $userAgent
     */
    public function __construct($userAgent)
    {
        // Set user agent string
        $this->userAgent = $userAgent;

        // Parse user agent string
        $this->parse();
    }


    protected function parse()
    {
        $data = app(Browscap::class)->getBrowser($this->userAgent);
        //dd($data);
        foreach ($data as $key => $value) {
            // Force lowercase on key
            $key = strtolower($key);

            // Remove whitespaces from value
            $value = trim($value);

            // Switch it up!
            switch ($key) {
                case 'ismobiledevice':
                    $this->isMobile = !empty($value);
                    break;
                case 'istablet':
                    $this->isTablet = !empty($value);
                    break;
                case 'crawler':
                    $this->isCrawler = !empty($value);
                    break;
                case 'majorver':
                    $this->majorVersion = (int) $value;
                    break;
                case 'minorver':
                    $this->minorVersion = (int) $value;
                    break;
                case 'device_type':
                    $this->device = $value;
                    break;
                case 'device_pointing_method':
                    $this->devicePointer = $value;
                    break;
                case 'browser_maker':
                    $this->publisher = $value;
                    break;
                case 'comment':
                    $this->browserWithVersion = $value;
                    break;
                case 'browser':
                case 'platform':
                    $this->{$key} = $value;
                    break;
            }
        }

        // Set version and if major and minor
        // wasn't present in the data array
        // we'll set them as well
        $this->setVersion($data->Version);
    }

    /**
     * Set version of browser
     *
     * @author Morten Rugaard <moru@nodes.dk>
     *
     * @access protected
     * @param  string $version
     * @return $this
     */
    protected function setVersion($version)
    {
        // Set version of browser
        $this->version = $version;

        // If major and minor versions hasn't been set
        // we'll set them by parsing version.
        //
        // If version doesn't contain a "." then we'll only
        // set the major version.
        if (empty($this->majorVersion) && strpos($version, '.') !== false) {
            // Split version by comma
            $version = explode('.', $version);

            // Set major and minor version
            $this->majorVersion = (int) $version[0];
            $this->minorVersion = (int) $version[1];
        } else {
            $this->majorVersion = (int) $version;
        }

        return $this;
    }

    /**
     * Retrieve user agent string
     *
     * @author Morten Rugaard <moru@nodes.dk>
     *
     * @access public
     * @return string
     */
    public function getUserAgent()
    {
        return $this->userAgent;
    }

    /**
     * Retrieve browser name
     *
     * @author Morten Rugaard <moru@nodes.dk>
     *
     * @access public
     * @return string
     */
    public function getBrowser()
    {
        return $this->browser;
    }

    /**
     * Retrieve browser version
     *
     * @author Morten Rugaard <moru@nodes.dk>
     *
     * @access public
     * @return integer
     */
    public function getVersion()
    {
        return $this->version;
    }

    /**
     * Retrieve browser's major version
     *
     * @author Morten Rugaard <moru@nodes.dk>
     *
     * @access public
     * @return integer
     */
    public function getMajorVersion()
    {
        return $this->majorVersion;
    }

    /**
     * Retrieve browser's minor version
     *
     * @author Morten Rugaard <moru@nodes.dk>
     *
     * @access public
     * @return integer
     */
    public function getMinorVersion()
    {
        return $this->minorVersion;
    }

    /**
     * Retrieve browser with version
     *
     * @author Morten Rugaard <moru@nodes.dk>
     *
     * @access public
     * @return string
     */
    public function getBrowserWithVersion()
    {
        return $this->browserWithVersion;
    }

    /**
     * Retrieve browser publisher
     *
     * @author Morten Rugaard <moru@nodes.dk>
     *
     * @access public
     * @return string
     */
    public function getPublisher()
    {
        return $this->publisher;
    }

    /**
     * Retrieve platform
     *
     * @author Morten Rugaard <moru@nodes.dk>
     *
     * @access public
     * @return string
     */
    public function getPlatform()
    {
        return $this->getPlatform;
    }

    /**
     * Retrieve device type
     *
     * @author Morten Rugaard <moru@nodes.dk>
     *
     * @access public
     * @return string
     */
    public function getDevice()
    {
        return $this->device;
    }

    /**
     * Retrieve device pointing method
     *
     * @author Morten Rugaard <moru@nodes.dk>
     *
     * @access public
     * @return string
     */
    public function getDevicePointer()
    {
        return $this->devicePointer;
    }

    /**
     * Did request come from a mobile
     *
     * @author Morten Rugaard <moru@nodes.dk>
     *
     * @access public
     * @return boolean
     */
    public function isMobile()
    {
        return $this->isMobile;
    }

    /**
     * Did request come from a tablet
     *
     * @author Morten Rugaard <moru@nodes.dk>
     *
     * @access public
     * @return boolean
     */
    public function isTablet()
    {
        return $this->isTablet;
    }

    /**
     * Did request come from a crawler
     *
     * @author Morten Rugaard <moru@nodes.dk>
     *
     * @access public
     * @return boolean
     */
    public function isCrawler()
    {
        return $this->isCrawler;
    }
}