<?php
if (!function_exists('nodes_install_service_provider')) {
    /**
     * Install service provider for a Nodes Package
     *
     * @author Morten Rugaard <moru@nodes.dk>
     *
     * @param  string $packageName
     * @param  string $serviceProviderFilename
     * @return void
     */
    function nodes_install_service_provider($packageName, $serviceProviderFilename = 'ServiceProvider.php')
    {
        // Instantiate Install Package handler
        $installPackage = app(\Nodes\Support\InstallPackage::class);

        // Install service provider for package
        $installPackage->setPackageName($packageName)->installServiceProvider($serviceProviderFilename);
    }
}