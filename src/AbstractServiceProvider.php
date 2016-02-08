<?php
namespace Nodes;

use Exception;
use Illuminate\Console\Command as IlluminateCommand;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\ServiceProvider as IlluminateServiceProvider;
use League\Flysystem\Adapter\Local as LocalAdapter;
use League\Flysystem\Filesystem as Flysystem;
use League\Flysystem\MountManager;
use Nodes\Exceptions\InstallPackageException;
use Nodes\Support\InstallPackage as NodesInstaller;
use Symfony\Component\Console\Input\ArgvInput as SymfonyConsoleInput;
use Symfony\Component\Console\Output\ConsoleOutput as SymfonyConsoleOutput;

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
     * Facades to install
     *
     * @var array
     */
    protected $facades = [];

    /**
     * Artisan commands to register
     *
     * @var array
     */
    protected $commands = [];

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
     * Array of migrations to copy
     *
     * @var array
     */
    protected $migrations = [];

    /**
     * Array of seeders to copy
     *
     * @var array
     */
    protected $seeders = [];

    /**
     * The filesystem instance
     *
     * @var \Illuminate\Filesystem\Filesystem
     */
    protected $files;

    /**
     * Nodes installer
     *
     * @var \Nodes\Support\InstallPackage
     */
    protected $installer;

    /**
     * Current console command
     *
     * @var \Illuminate\Console\Command
     */
    protected $command;

    /**
     * Register the service provider
     *
     * @author Morten Rugaard <moru@nodes.dk>
     *
     * @access public
     * @return void
     */
    public function register()
    {
        $this->registerCommands();
    }

    /**
     * Register Artisan Commands
     *
     * @author Morten Rugaard <moru@nodes.dk>
     *
     * @access public
     * @return $this
     */
    public function registerCommands()
    {
        $this->commands($this->commands);
        return $this;
    }

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
        if (empty($this->getCommand())) {
            throw new InstallPackageException('Could not run install sequence. Reason: Missing Console Output reference.');
        }

        // Run install methods
        $this->prepareInstall();
        $this->installConfigs();
        $this->installFacades();
        $this->installScaffolding();
        $this->installViews();
        $this->installAssets();
        $this->installCustom();
        $this->installDatabase();
        $this->finishInstall();
    }

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
        if (empty($this->configs)) {
            return;
        }

        // Copy config files to application
        $this->copyFilesAndDirectories($this->configs);
    }

    /**
     * Install package facades
     *
     * @author Morten Rugaard <moru@nodes.dk>
     *
     * @access protected
     * @return void
     */
    protected function installFacades()
    {
        if (empty($this->facades)) {
            return;
        }

        // Install package facades
        $this->getInstaller()->installFacades($this->getFacades());
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
        if (empty($this->views)) {
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
        if (empty($this->assets)) {
            return;
        }

        // Copy assets files to application
        $this->copyFilesAndDirectories($this->assets);
    }

    /**
     * Install database migrations and seeders
     *
     * @author Morten Rugaard <moru@nodes.dk>
     *
     * @access protected
     * @return void
     */
    protected function installDatabase()
    {
        $this->installDatabaseMigrations();
        $this->installDatabaseSeeders();
        $this->runDatabaseMigrationsAndSeeders();
    }

    /**
     * Install database migration files
     *
     * @author Morten Rugaard <moru@nodes.dk>
     *
     * @access protected
     * @return void
     */
    protected function installDatabaseMigrations()
    {
        if (empty($this->migrations)) {
            return;
        }

        // Copy migration files to application
        $this->copyFilesAndDirectories($this->migrations);
    }

    /**
     * Install database seeder files
     *
     * @author Morten Rugaard <moru@nodes.dk>
     *
     * @access protected
     * @return void
     */
    protected function installDatabaseSeeders()
    {
        if (empty($this->seeders)) {
            return;
        }

        // Copy seeder files to application
        $this->copyFilesAndDirectories($this->seeders);
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
     * Prepare install
     *
     * @author Morten Rugaard <moru@nodes.dk>
     *
     * @access protected
     * @return void
     */
    protected function prepareInstall() {}

    /**
     * Finish install
     *
     * @author Morten Rugaard <moru@nodes.dk>
     *
     * @access public
     * @return void
     */
    protected function finishInstall() {}

    /**
     * Run database migrations and seeders
     *
     * @author Morten Rugaard <moru@nodes.dk>
     *
     * @access protected
     * @return void
     */
    final protected function runDatabaseMigrationsAndSeeders()
    {
        // Ask to migrate the database,
        // if we've copied any migration files to the application.
        if (!empty($this->migrations) && $this->getCommand()->confirm('Do you wish to migrate your database?', true)) {
            try {
                $this->getCommand()->call('migrate');
            } catch (Exception $e) {
                $this->getCommand()->error(sprintf('Could not migrate your database. Reason: %s', $e->getMessage()));
            }
        }

        // Before we ask user to seed the database
        // we need to have look in the $seeders array
        // to remove folders from the array
        $seeders = [];
        foreach ($this->seeders as $seeder) {
            if (is_dir(base_path($seeder))) {
                continue;
            }
            $seeders[] = $seeder;
        }

        // Ask to seed the database,
        // if we've copied any migration files to the application.
        if (!empty($seeders) && $this->getCommand()->confirm('Do you wish to seed your database?', true)) {
            // Load seeders directory so new seeders are available
            load_directory($this->getInstaller()->getBasePath('database/seeds/'));

            // Run package seeders
            foreach ($seeders as $seeder) {
                try {
                    $seederFilename = substr($seeder, strrpos($seeder, '/') + 1);
                    $this->getCommand()->call('db:seed', [
                        '--class' => substr($seederFilename, 0, strrpos($seederFilename, '.'))
                    ]);
                } catch (Exception $e) {
                    $this->getCommand()->error(sprintf('Could not seed database. Reason: %s', $e->getMessage()));
                }
            }
        }
    }

    /**
     * Copy files and/or directories to application
     *
     * @author Morten Rugaard <moru@nodes.dk>
     *
     * @access protected
     * @param  array $data
     * @return void
     */
    final protected function copyFilesAndDirectories(array $data)
    {
        foreach ($data as $from => $to) {
            // Prepare $from and $to paths
            $from = base_path(sprintf('vendor/%s/%s/%s', $this->vendor, $this->package, $from));
            $to = base_path($to);

            // Copy files or directory to application
            if ($this->files->isFile($from)) {
                $this->publishFile($from, $to);
            } elseif ($this->files->isDirectory($from)) {
                $this->publishDirectory($from, $to);
            } else {
                $this->getCommand()->error(sprintf('Could not locate path: <%s>', $from));
            }
        }
    }

    /**
     * Publish file to application
     *
     * @author Morten Rugaard <moru@nodes.dk>
     *
     * @access protected
     * @param  string $from
     * @param  string $to
     * @return void
     */
    final protected function publishFile($from, $to)
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
        $this->getCommand()->line(
            sprintf('<info>Copied %s</info> <comment>[%s]</comment> <info>To</info> <comment>[%s]</comment>',
                'File', str_replace(base_path(), '', realpath($from)), str_replace(base_path(), '', realpath($to)))
        );
    }

    /**
     * Publish directory to application
     *
     * @author Morten Rugaard <moru@nodes.dk>
     *
     * @access protected
     * @param  string $from
     * @param  string $to
     * @return void
     */
    final protected function publishDirectory($from, $to)
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
        $this->getCommand()->line(
            sprintf('<info>Copied %s</info> <comment>[%s]</comment> <info>To</info> <comment>[%s]</comment>',
                'Directory', str_replace(base_path(), '', realpath($from)), str_replace(base_path(), '', realpath($to)))
        );
    }

    /**
     * callArtisanCommand
     *
     * @author Morten Rugaard <moru@nodes.dk>
     *
     * @access protected
     * @param  string $command
     * @param  array $params
     * @return integer
     */
    final protected function callArtisanCommand($command, array $params = [])
    {
        // Default input arguments
        $inputArguments = ['NodesInstall', $command];

        // Add artisan parameters to input arguments
        if (!empty($params) && is_array($params)) {
            $inputArguments[] = implode(' ', $params);
        }

        // Execute Artisan command
        return $this->getInstaller()->getArtisan()->handle(
            new SymfonyConsoleInput($inputArguments),
            new SymfonyConsoleOutput
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
     * Retrieve facades to install
     *
     * @author Morten Rugaard <moru@nodes.dk>
     *
     * @final
     * @access public
     * @return array
     */
    final public function getFacades()
    {
        return (array) $this->facades;
    }

    /**
     * Set installer instance
     *
     * @author Morten Rugaard <moru@nodes.dk>
     *
     * @access public
     * @param  \Nodes\Support\InstallPackage $installer
     * @return $this
     */
    public function setInstaller(NodesInstaller $installer)
    {
        $this->installer = $installer;
        return $this;
    }

    /**
     * Retrieve installer instance
     *
     * @author Morten Rugaard <moru@nodes.dk>
     *
     * @access public
     * @return \Nodes\Support\InstallPackage
     */
    public function getInstaller()
    {
        return $this->installer;
    }

    /**
     * Set current console command
     *
     * @author Morten Rugaard <moru@nodes.dk>
     *
     * @access public
     * @param  \Illuminate\Console\Command $command
     * @return $this
     */
    public function setCommand(IlluminateCommand $command)
    {
        $this->command = $command;
        return $this;
    }

    /**
     * Retrieve current console command
     *
     * @author Morten Rugaard <moru@nodes.dk>
     *
     * @access public
     * @return \Illuminate\Console\Command
     */
    public function getCommand()
    {
        return $this->command;
    }
}