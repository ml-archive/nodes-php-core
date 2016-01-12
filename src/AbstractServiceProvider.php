<?php
namespace Nodes;

use Illuminate\Console\OutputStyle as IlluminateConsoleOutput;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\ServiceProvider as IlluminateServiceProvider;
use League\Flysystem\Adapter\Local as LocalAdapter;
use League\Flysystem\Filesystem as Flysystem;
use League\Flysystem\MountManager;
use Nodes\Exceptions\InstallPackageException;

/**
 * Class ServiceProvider
 *
 * @abstract
 * @package Nodes\Core
 */
abstract class AbstractServiceProvider extends IlluminateServiceProvider
{
    /**
     * Vendor name
     *
     * @var string
     */
    protected $vendor = 'nodes';

    /**
     * Package name
     *
     * @var string|null
     */
    protected $package = null;

    /**
     * Array of configs to copy
     *
     * @var array
     */
    protected $configs = [];

    /**
     * Array of views to copy
     *
     * @var array
     */
    protected $views = [];

    /**
     * Array of assets to copy
     *
     * @var array
     */
    protected $assets = [];

    /**
     * The filesystem instance
     *
     * @var \Illuminate\Filesystem\Filesystem
     */
    protected $files;

    /**
     * The output interface implementation
     *
     * @var \Illuminate\Console\OutputStyle
     */
    protected $output;

    /**
     * Install service provider
     *
     * @author Morten Rugaard <moru@nodes.dk>
     *
     * @final
     * @access public
     * @return void
     * @throws \Nodes\Exceptions\InstallPackageException
     */
    final public function install()
    {
        // Initiate Flysystem
        $this->files = new Filesystem;

        // Make sure we have the Console Output style
        // before continuing with the install sequence
        if (empty($this->output)) {
            throw new InstallPackageException('Could not run install sequence. Reason: Missing Console Output reference.');
        }

        // Run install methods
        $this->installConfigs();
        $this->installScaffolding();
        $this->installViews();
        $this->installAssets();
        $this->installCustom();
    }

    /**
     * Register the service provider
     *
     * @author Morten Rugaard <moru@nodes.dk>
     *
     * @access public
     * @return void
     */
    public function register() {}

    /**
     * Copy configs to application
     *
     * @author Morten Rugaard <moru@nodes.dk>
     *
     * @access protected
     * @return void
     */
    protected function installConfigs()
    {
        // Generate question
        $question = sprintf('Do you wish to copy config files from the package [%s] to your project? Note: Existing files will be overwritten.', sprintf('%s/%s', $this->vendor, $this->package));

        // Make user confirm copying of config files
        if (empty($this->configs) || !$this->output->confirm($question, true)) {
            return;
        }

        // Copy config files to application
        $this->copyFilesAndDirectories($this->configs);
    }

    /**
     * Copy views to application
     *
     * @author Morten Rugaard <moru@nodes.dk>
     *
     * @access protected
     * @return void
     */
    protected function installViews()
    {
        // Generate question
        $question = sprintf('Do you wish to copy view files from the package [%s] to your project? Note: Existing files will be overwritten.', sprintf('%s/%s', $this->vendor, $this->package));

        // Make user confirm copying of view files
        if (empty($this->views) || !$this->output->confirm($question, true)) {
            return;
        }

        // Copy view files to application
        $this->copyFilesAndDirectories($this->views);
    }

    /**
     * Copy assets to application
     *
     * @author Morten Rugaard <moru@nodes.dk>
     *
     * @access protected
     * @return void
     */
    protected function installAssets()
    {
        // Generate question
        $question = sprintf('Do you wish to copy assets files from the package [%s] to your project? Note: Existing files will be overwritten.', sprintf('%s/%s', $this->vendor, $this->package));

        // Make user confirm copying of assets files
        if (empty($this->assets) || !$this->output->confirm($question, true)) {
            return;
        }

        // Copy assets files to application
        $this->copyFilesAndDirectories($this->assets);
    }

    /**
     * Install scaffolding
     *
     * @author Morten Rugaard <moru@nodes.dk>
     *
     * @abstract
     * @access protected
     * @return void
     */
    protected function installScaffolding() {}

    /**
     * Install custom stuff
     *
     * @author Morten Rugaard <moru@nodes.dk>
     *
     * @abstract
     * @access protected
     * @return void
     */
    protected function installCustom() {}

    /**
     * Copy files and/or directories to application
     *
     * @author Morten Rugaard <moru@nodes.dk>
     *
     * @access private
     * @param  array $data
     * @return void
     */
    private function copyFilesAndDirectories(array $data)
    {
        foreach ($data as $from => $to) {
            // Prepare $from and $to paths
            $from = base_path(sprintf('%s/%s/%s', $this->vendor, $this->package, $from));
            $to = config_path($to);

            // Copy files or directory to application
            if ($this->files->isFile($from)) {
                $this->publishFile($from, $to);
            } elseif ($this->files->isDirectory($from)) {
                $this->publishDirectory($from, $to);
            } else {
                $this->output->error(sprintf('Could not locate path: <%s>', $from));
            }
        }
    }

    /**
     * Publish file to application
     *
     * @author Morten Rugaard <moru@nodes.dk>
     *
     * @access private
     * @param  string $from
     * @param  string $to
     * @return void
     */
    private function publishFile($from, $to)
    {
        // If destination directory doesn't exist,
        // we'll create before copying the config files
        $directoryDestination = dirname($to);
        if (!$this->files->isDirectory($directoryDestination)) {
            $this->files->makeDirectory($directoryDestination, 0755, true);
        }

        // Copy file to application
        $this->files->copy($from, $to);

        // Output status message
        $this->output->writeln(
            sprintf('<info>Copied %s</info> <comment>[%s]</comment> <info>To</info> <comment>[%s]</comment>',
            'File', str_replace(base_path(), '', realpath($from)), str_replace(base_path(), '', realpath($to)))
        );
    }

    /**
     * Publish directory to application
     *
     * @author Morten Rugaard <moru@nodes.dk>
     *
     * @param $from
     * @param $to
     * @return void
     */
    private function publishDirectory($from, $to)
    {
        // Load mount manager
        $manager = new MountManager([
            'from' => new Flysystem(new LocalAdapter($from)),
            'to' => new Flysystem(new LocalAdapter($to)),
        ]);

        // Copy directory to application
        foreach ($manager->listContents('from://', true) as $file) {
            if ($file['type'] !== 'file') {
                continue;
            }
            $manager->put(sprintf('to://%s', $file['path']), $manager->read(sprintf('from://%s', $file['path'])));
        }

        // Output status message
        $this->output->writeln(
            sprintf('<info>Copied %s</info> <comment>[%s]</comment> <info>To</info> <comment>[%s]</comment>',
            'Directory', str_replace(base_path(), '', realpath($from)), str_replace(base_path(), '', realpath($to)))
        );
    }

    /**
     * Set vendor name
     *
     * @author Morten Rugaard <moru@nodes.dk>
     *
     * @final
     * @access public
     * @param  string $vendor
     * @return $this
     */
    final public function setVendor($vendor)
    {
        $this->vendor = $vendor;
        return $this;
    }

    /**
     * Retrieve vendor name
     *
     * @author Morten Rugaard <moru@nodes.dk>
     *
     * @final
     * @access public
     * @return string
     */
    final public function getVendor()
    {
        return $this->vendor;
    }

    /**
     * Set package name
     *
     * @author Morten Rugaard <moru@nodes.dk>
     *
     * @final
     * @access public
     * @param  string $package
     * @return $this
     */
    final public function setPackage($package)
    {
        $this->package = $package;
        return $this;
    }

    /**
     * Retrieve package name
     *
     * @author Morten Rugaard <moru@nodes.dk>
     *
     * @final
     * @access public
     * @return string|null
     */
    final public function getPackage()
    {
        return $this->package;
    }

    /**
     * Set console output style, which is used
     * by our install method.
     *
     * @author Morten Rugaard <moru@nodes.dk>
     *
     * @final
     * @access public
     * @param \Illuminate\Console\OutputStyle $output
     * @return $this
     */
    final public function setOutput(IlluminateConsoleOutput $output)
    {
        $this->output = $output;
        return $this;
    }
}