<?php

namespace Monorepo\Composer;

use Composer\Installer\InstallerInterface;
use Composer\Package\PackageInterface;
use Composer\Repository\InstalledRepositoryInterface;

class MonorepoInstaller implements InstallerInterface
{
    public function supports($packageType): bool
    {
        return $packageType === 'monorepo';
    }

    public function isInstalled(InstalledRepositoryInterface $repo, PackageInterface $package): bool
    {
        return true;
    }

    public function download(PackageInterface $package, ?PackageInterface $prevPackage = null)
    {
    }

    public function prepare($type, PackageInterface $package, ?PackageInterface $prevPackage = null)
    {
    }

    public function install(InstalledRepositoryInterface $repo, PackageInterface $package)
    {
    }

    public function update(InstalledRepositoryInterface $repo, PackageInterface $initial, PackageInterface $target)
    {
    }

    public function uninstall(InstalledRepositoryInterface $repo, PackageInterface $package)
    {
    }

    public function cleanup($type, PackageInterface $package, ?PackageInterface $prevPackage = null)
    {
    }

    public function getInstallPath(PackageInterface $package): string
    {
        return $package->getPrettyName(); // Monorepo package names are directory paths.
    }
}
