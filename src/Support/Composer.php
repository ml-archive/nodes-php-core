<?php
namespace Nodes\Support;

use Composer\Composer as ComposerInstance;
use Composer\EventDispatcher\EventSubscriberInterface as ComposerEventSubscriberContract;
use Composer\Installer\PackageEvent as ComposerPackageEvent;
use Composer\Installer\PackageEvents as ComposerPackageEvents;
use Composer\IO\IOInterface as ComposerIOContract;
use Composer\Plugin\CommandEvent as ComposerPluginCommandEvent;
use Composer\Plugin\PluginEvents as ComposerPluginEvents;
use Composer\Plugin\PluginInterface;
use Composer\Plugin\PreFileDownloadEvent as ComposerPluginPreFileDownloadEvent;
use Composer\Script\Event as ComposerScriptEvent;
use Composer\Script\ScriptEvents as ComposerScriptEvents;

/**
 * Class Composer
 *
 * @package Nodes\Support
 */
class Composer implements PluginInterface, ComposerEventSubscriberContract
{
    /**
     * Composer instance
     *
     * @var \Composer\Composer
     */
    protected $composer;

    /**
     * Composer IO instance
     *
     * @var \Composer\IO\IOInterface
     */
    protected $io;

    /**
     * Activate is called after the plugin is loaded
     *
     * @author Morten Rugaard <moru@nodes.dk>
     *
     * @access public
     * @param  \Composer\Composer  $composer
     * @param  \Composer\IO\IOInterface $io
     * @return void
     */
    public function activate(ComposerInstance $composer, ComposerIOContract $io)
    {
        $this->composer = $composer;
        $this->io = $io;
    }

    /**
     * Returns an array of event names this subscriber wants to listen to.
     *
     * @author Morten Rugaard <moru@nodes.dk>
     *
     * @static
     * @access public
     * @return array
     */
    public static function getSubscribedEvents()
    {
        return [
            //ComposerPackageEvents::POST_PACKAGE_INSTALL => 'nodesInstallPackage'
            ComposerPluginEvents::COMMAND => 'onCommand',
            ComposerPluginEvents::PRE_FILE_DOWNLOAD => 'onPreFileDownload',
            ComposerScriptEvents::PRE_UPDATE_CMD => 'onPreUpdateCmd',
            ComposerScriptEvents::POST_UPDATE_CMD => 'onPostUpdateCmd',
            ComposerPackageEvents::PRE_PACKAGE_UPDATE => 'onPrePackageUpdate',
            ComposerPackageEvents::POST_PACKAGE_UPDATE => 'onPostPackageUpdate',
            ComposerPackageEvents::PRE_PACKAGE_INSTALL => 'onPrePackageInstall',
            ComposerPackageEvents::POST_PACKAGE_INSTALL => 'onPostPackageInstall',
        ];
    }

    public function onCommand(ComposerPluginCommandEvent $event)
    {
        $this->io->write(__CLASS__ . '::' . __METHOD__ . "() -> " . $event->getName() . ' / ' . $event->getCommandName() . ' / ' . implode(', ', $event->getArguments()) . ' / ' . $event->getFlags());
    }

    public function onPreFileDownload(ComposerPluginPreFileDownloadEvent $event)
    {
        $this->io->write(__CLASS__ . '::' . __METHOD__ . "() -> " . $event->getName() . ' / ' . $event->getCommandName() . '/ ' . $event->getProcessedUrl() . ' / ' . implode(', ', $event->get()) . ' / ' . $event->getFlags());
    }

    public function onPreUpdateCmd(ComposerScriptEvent $event)
    {
        $this->io->write(__CLASS__ . '::' . __METHOD__ . "() -> " . $event->getName() . ' / ' . $event->getCommandName() . ' / ' . $event->getID() . ' / ' . implode(', ', $event->getArguments()) . ' / ' . $event->getFlags());
    }

    public function onPostUpdateCmd(ComposerScriptEvent $event)
    {
        $this->io->write(__CLASS__ . '::' . __METHOD__ . "() -> " . $event->getName() . ' / ' . $event->getCommandName() . ' / ' . $event->getID() . ' / ' . implode(', ', $event->getArguments()) . ' / ' . $event->getFlags());
    }

    public function onPrePackageUpdate(ComposerPackageEvent $event)
    {
        $this->io->write(__CLASS__ . '::' . __METHOD__ . "() -> " . $event->getName() . ' / ' . $event->getCommandName() . ' / ' . implode(', ', $event->getArguments()) . ' / ' . $event->getFlags());
    }

    public function onPostPackageUpdate(ComposerPackageEvent $event)
    {
        $this->io->write(__CLASS__ . '::' . __METHOD__ . "() -> " . $event->getName() . ' / ' . $event->getCommandName() . ' / ' . implode(', ', $event->getArguments()) . ' / ' . $event->getFlags());
    }

    public function onPrePackageInstall(ComposerPackageEvent $event)
    {
        $this->io->write(__CLASS__ . '::' . __METHOD__ . "() -> " . $event->getName() . ' / ' . $event->getCommandName() . ' / ' . implode(', ', $event->getArguments()) . ' / ' . $event->getFlags());
    }

    public function onPostPackageInstall(ComposerPackageEvent $event)
    {
        $this->io->write(__CLASS__ . '::' . __METHOD__ . "() -> " . $event->getName() . ' / ' . $event->getCommandName() . ' / ' . implode(', ', $event->getArguments()) . ' / ' . $event->getFlags());
    }
}