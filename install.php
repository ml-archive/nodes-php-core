<?php
/*
|--------------------------------------------------------------------------
| Prepare arguments
|--------------------------------------------------------------------------
|
| Remove file path from arguments array and prepare data
| so it's only contains the package name and maybe service provider
|
*/
list($package) = array_slice($argv, 1);

/*
|--------------------------------------------------------------------------
| Require files
|--------------------------------------------------------------------------
|
| Load required files before bootstrapping application.
|
*/
require __DIR__ . '/../../../bootstrap/autoload.php';

$app = require_once __DIR__ . '/../../../bootstrap/app.php';

/*
|--------------------------------------------------------------------------
| Nodes installer
|--------------------------------------------------------------------------
|
| Instantiate Nodes "Install package" helper.
|
*/
$installPackageHelper = new \Nodes\Support\InstallPackage;

// Add "Nodes Service Provider" to Laravel's app.php config
$installPackageHelper->addNodesServiceProvider();

/*
|--------------------------------------------------------------------------
| Bootstrap The Artisan Application
|--------------------------------------------------------------------------
|
| When we run the console application, the current CLI command will be
| executed in this console and the response sent back to a terminal
| or another output device for the developers. Here goes nothing!
|
*/

$kernel = $app->make(\Illuminate\Contracts\Console\Kernel::class);

$status = $kernel->handle(
    $input = new Symfony\Component\Console\Input\ArgvInput,
    new Symfony\Component\Console\Output\ConsoleOutput
);

/*
|--------------------------------------------------------------------------
| Shutdown The Application
|--------------------------------------------------------------------------
|
| Once Artisan has finished running. We will fire off the shutdown events
| so that any final work may be done by the application before we shut
| down the process. This is the last thing to happen to the request.
|
*/

$kernel->terminate($input, $status);

exit($status);