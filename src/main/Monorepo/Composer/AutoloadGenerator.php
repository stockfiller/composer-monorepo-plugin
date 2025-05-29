<?php

namespace Monorepo\Composer;

use Composer\Config;
use Composer\Installer\InstallationManager;
use Composer\Package\Locker;
use Composer\Package\PackageInterface;
use Composer\Package\RootPackageInterface;
use Composer\Repository\InstalledRepositoryInterface;

class AutoloadGenerator extends \Composer\Autoload\AutoloadGenerator
{
    public function buildPackageMap(InstallationManager $installationManager, PackageInterface $mainPackage, array $packages): array
    {
        $packageMap = parent::buildPackageMap($installationManager, $mainPackage, $packages);

        $packageMap[0][1] = $installationManager->getInstallPath($mainPackage); // hack the install path

        return $packageMap;
    }

    // Overrides the $suffix to one that is unique within a monorepo module
    //
    // This is necessary for compatibility with paratest. The default behavior of composer v2 uses the hash
    // of the composer.json file as the suffix for the ComposerAutoloaderInit class. This results in duplicate
    // class names throughout the monorepo.
    //
    // Replacing the default suffix with the hash of the targetDir provides sufficient uniqueness across each
    // of the modules.
    public function dump(Config $config, InstalledRepositoryInterface $localRepo, RootPackageInterface $rootPackage, InstallationManager $installationManager, string $targetDir, bool $scanPsrPackages = false, ?string $suffix = null, ?Locker $locker = null, bool $strictAmbiguous = false) {
	$moduleSuffix = md5($targetDir);
        return parent::dump($config, $localRepo, $rootPackage, $installationManager, $targetDir, $scanPsrPackages, $moduleSuffix, $locker, $strictAmbiguous);
    }

    protected function getFileIdentifier(PackageInterface $package, $path): string
    {
        $extra = $package->getExtra();

        return md5(
            (isset($extra['monorepo']['original_name']) ? $extra['monorepo']['original_name'] : $package->getName()) .
            ':' .
            $path
        );
    }

    protected function filterPackageMap(array $packageMap, RootPackageInterface $mainPackage): array
    {
        return $packageMap;
    }

    protected function getAutoloadFile($vendorPathToTargetDirCode, $suffix): string
    {
        $code = parent::getAutoloadFile($vendorPathToTargetDirCode, $suffix);

        $code = str_replace('<?php', <<<PHP
<?php
putenv('COMPOSER_VENDOR_DIR=' . __DIR__);

PHP
            , $code);

        return $code;
    }
}
