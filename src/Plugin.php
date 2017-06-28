<?php

namespace Psr4Folders;

use Composer\Composer;
use Composer\IO\IOInterface;
use Composer\Plugin\PluginInterface;
use Composer\Script\ScriptEvents;
use Composer\EventDispatcher\EventSubscriberInterface;
use Composer\Util\Filesystem;
use Symfony\Component\Finder\Finder;

class Plugin implements PluginInterface, EventSubscriberInterface
{
    /**
     * @var \Composer\Composer
     */
    protected $composer;

    public static function getSubscribedEvents()
    {
        return [
            ScriptEvents::PRE_AUTOLOAD_DUMP => 'preAutoloadDump',
        ];
    }

    public function activate(Composer $composer, IOInterface $io)
    {
        $this->composer = $composer;
    }

    public function preAutoloadDump()
    {
        $ds = DIRECTORY_SEPARATOR;
        $package = $this->composer->getPackage();
        $filesystem = new Filesystem();
        $basePath = $filesystem->normalizePath(realpath(realpath(getcwd())));

        $autoload = $package->getAutoload();

        if (isset($autoload['psr-4-folders'])) {
            if (!isset($autoload['psr-4'])) {
                $autoload['psr-4'] = [];
            }

            foreach ($autoload['psr-4-folders'] as $folder) {
                $folderPath = "{$basePath}{$ds}{$folder}";

                foreach (Finder::create()->directories()->depth(0)->in($folderPath) as $directory) {
                    $name = substr($directory, strlen($folderPath) + 1);

                    if (is_dir("{$directory}{$ds}src")) {
                        $autoload['psr-4'][$name . '\\'] = "{$folder}{$name}{$ds}src{$ds}";
                    } else {
                        $autoload['psr-4'][$name . '\\'] = "{$folder}{$name}{$ds}";
                    }

                    if (file_exists("{$directory}{$ds}functions.php")) {
                        $autoload['files'][] = "{$folder}{$name}{$ds}functions.php";
                    }
                }
            }

            $package->setAutoload($autoload);
        }
    }
}
