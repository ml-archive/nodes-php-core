<?php

namespace Nodes\Support\Meta;

use Nodes\Exceptions\BadRequestException;

/**
 * Class Nodes.
 */
class Meta
{
    /**
     * @var string
     */
    protected $platform;

    /**
     * @var string
     */
    protected $environment;

    /**
     * Version number.
     *
     * @var string
     */
    protected $version = 0;

    /**
     * Major version number.
     *
     * @var int
     */
    protected $majorVersion = 0;

    /**
     * Minor version number.
     *
     * @var int
     */
    protected $minorVersion = 0;

    /**
     * Patch version number.
     *
     * @var int
     */
    protected $patchVersion = 0;

    /**
     * @var string|null
     */
    protected $deviceOsVersion;

    /**
     * @var string
     */
    protected $device;

    const PLATFORM_IOS = 'ios';

    const PLATFORM_ANDROID = 'android';

    const PLATFORM_WINDOWS = 'windows';

    const PLATFORM_WEB = 'web';

    const PLATFORMS = [
        self::PLATFORM_IOS,
        self::PLATFORM_ANDROID,
        self::PLATFORM_WINDOWS,
        self::PLATFORM_WEB,
    ];

    const ENV_DEVELOPMENT = 'development';

    const ENV_STAGING = 'staging';

    const ENV_PRODUCTION = 'production';

    const ENVIRONMENTS = [
        self::ENV_DEVELOPMENT,
        self::ENV_STAGING,
        self::ENV_PRODUCTION,
    ];

    /**
     * Meta constructor
     *
     * @author Casper Rasmussen <cr@nodes.dk>
     * @access public
     *
     * @param $header
     *
     * @throws BadRequestException
     */
    public function __construct($header)
    {
        $headerArr = explode(';', $header);

        // Parse platform
        if ( ! isset($headerArr[0]) || ! in_array($headerArr[0], self::PLATFORMS)) {
            throw new BadRequestException('Platform is not supported, should be: '.implode(',', self::PLATFORMS));
        }

        $this->platform = $headerArr[0];

        // Web does not have further requirements, since they have a normal
        if ($this->platform == self::PLATFORM_WEB) {
            return;
        }

        // Parse env
        if ( ! isset($headerArr[1]) || ! in_array($headerArr[1], self::ENVIRONMENTS)) {
            throw new BadRequestException('Environment is not supported, should be: '.implode(',', self::ENVIRONMENTS));
        }

        $this->environment = $headerArr[1];

        // Parse Build number
        if ( ! isset($headerArr[2])) {
            throw new BadRequestException('Missing version');
        }

        $this->version      = $headerArr[2];
        $versionArr         = explode('.', $this->version);
        $this->majorVersion = isset($versionArr[0]) ? $versionArr[0] : 0;
        $this->minorVersion = isset($versionArr[1]) ? $versionArr[1] : 0;
        $this->patchVersion = isset($versionArr[2]) ? $versionArr[2] : 0;

        // Parse device os version
        if ( ! isset($headerArr[3])) {
            throw new BadRequestException('Missing device os version');
        }

        $this->deviceOsVersion = $headerArr[3];

        // Parse device
        if ( ! isset($headerArr[4])) {
            throw new BadRequestException('Missing device');
        }

        $this->device = $headerArr[4];
    }
}
