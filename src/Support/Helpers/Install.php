<?php
if (!function_exists('nodes_install_service_provider')) {
    /**
     * Install service provider for a Nodes package
     *
     * @author Morten Rugaard <moru@nodes.dk>
     *
     * @param  string $vendorName
     * @param  string $packageName
     * @param  string $serviceProviderFilename
     * @return string
     */
    function nodes_install_service_provider($vendorName, $packageName, $serviceProviderFilename = 'ServiceProvider.php')
    {
        // Instantiate Install Package handler
        $installPackage = app(\Nodes\Support\InstallPackage::class);

        // Install service provider for package
        return $installPackage->setVendorName($vendorName)
                              ->setPackageName($packageName)
                              ->installServiceProvider($serviceProviderFilename);
    }
}

if (!function_exists('nodes_install_facades')) {
    /**
     * Install facades used by a Nodes package
     *
     * @author Morten Rugaard <moru@nodes.dk>
     *
     * @param  string                         $vendorName
     * @param  string                         $packageName
     * @param  \Nodes\AbstractServiceProvider $serviceProvider
     * @return boolean
     */
    function nodes_install_facades($vendorName, $packageName, \Nodes\AbstractServiceProvider $serviceProvider)
    {
        // Instantiate Install Package handler
        $installPackage = app(\Nodes\Support\InstallPackage::class);

        // Install service provider for package
        return $installPackage->setOutput($serviceProvider->getOutput())
                              ->setVendorName($vendorName)
                              ->setPackageName($packageName)
                              ->installFacades($serviceProvider->getFacades());
    }
}

if (!function_exists('nodes_is_package_installed')) {
    /**
     * Check if a Nodes package is already installed
     *
     * @author Morten Rugaard <moru@nodes.dk>
     *
     * @param  string $vendorName
     * @param  string $packageName
     * @param  string $serviceProviderFilename
     * @return boolean
     */
    function nodes_is_package_installed($vendorName, $packageName, $serviceProviderFilename = 'ServiceProvider.php')
    {
        // Instantiate Install Package handler
        $installPackage = app(\Nodes\Support\InstallPackage::class);

        // Check if package is already installed
        return $installPackage->setVendorName($vendorName)
                              ->setPackageName($packageName)
                              ->isPackageInstalled($serviceProviderFilename);
    }
}