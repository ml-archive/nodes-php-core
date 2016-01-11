<?php
if (!function_exists('nodes_install_service_provider')) {
    /**
     * Install service provider for a Nodes Package
     *
     * @author Morten Rugaard <moru@nodes.dk>
     *
     * @param  string $vendorName
     * @param  string $packageName
     * @param  string $serviceProviderFilename
     * @return void
     */
    function nodes_install_service_provider($vendorName, $packageName, $serviceProviderFilename = 'ServiceProvider.php')
    {
        // Instantiate Install Package handler
        $installPackage = app(\Nodes\Support\InstallPackage::class);

        // Install service provider for package
        $installPackage->setVendorName($vendorName)
                       ->setPackageName($packageName)
                       ->installServiceProvider($serviceProviderFilename);
    }
}