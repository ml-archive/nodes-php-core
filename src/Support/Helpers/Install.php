<?php

if (! function_exists('nodes_install_service_provider')) {
    /**
     * Install service provider for a Nodes package.
     *
     * @author Morten Rugaard <moru@nodes.dk>
     *
     * @param  string $package
     * @return string
     */
    function nodes_install_service_provider($package)
    {
        // Instantiate Install Package handler
        $installPackage = app(\Nodes\Support\InstallPackage::class);

        // Install service provider for package
        return $installPackage->setVendorPackageName($package)->installServiceProvider();
    }
}

if (! function_exists('nodes_install_facades')) {
    /**
     * Install facades used by a Nodes package.
     *
     * @author Morten Rugaard <moru@nodes.dk>
     *
     * @param  string                         $package
     * @param  \Nodes\AbstractServiceProvider $serviceProvider
     * @return bool
     */
    function nodes_install_facades($package, \Nodes\AbstractServiceProvider $serviceProvider)
    {
        // Instantiate Install Package handler
        $installPackage = app(\Nodes\Support\InstallPackage::class);

        // Install facades belonging to package
        return $installPackage->setVendorPackageName($package)->installFacades($serviceProvider->getFacades());
    }
}

if (! function_exists('nodes_is_package_installed')) {
    /**
     * Check if a Nodes package is already installed.
     *
     * @author Morten Rugaard <moru@nodes.dk>
     *
     * @param  string $package
     * @return bool
     */
    function nodes_is_package_installed($package)
    {
        // Instantiate Install Package handler
        $installPackage = app(\Nodes\Support\InstallPackage::class);

        // Check if package is already installed
        return $installPackage->setVendorPackageName($package)->isPackageInstalled();
    }
}
