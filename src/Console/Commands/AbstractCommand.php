<?php

namespace Flatbase\Console\Commands;

use Flatbase\Flatbase;
use Flatbase\Query\Query;
use Flatbase\Storage\Filesystem;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;

abstract class AbstractCommand extends Command
{
    /**
     * @var InputInterface
     */
    protected $input;
    /**
     * @var \Symfony\Component\Console\Output\OutputInterface
     */
    protected $output;
    /**
     * @var callable
     */
    protected $factory;

    /**
     * @{inheritdoc}
     */
    protected function configure()
    {
        parent::configure();

        $this
            ->addOption(
                'path',
                null,
                InputOption::VALUE_OPTIONAL,
                'Path of the database storage dir'
            )
            ->addOption(
                'config',
                null,
                InputOption::VALUE_OPTIONAL,
                'Path to a flatbase.json configuration file'
            )
        ;
    }

    /**
     * Get the Flatbase storage path from the --path console option
     *
     * @return string
     */
    protected function getStoragePath()
    {
        if ($override = $this->input->getOption('path')) {
            return $override;
        }
        $config = $this->getConfig();

        return getcwd().'/'.$config->path;
    }

    /**
     * Get the config data
     *
     * @return \stdClass
     */
    protected function getConfig()
    {
        $configPath = $this->input->getOption('config') ?: (getcwd().'/flatbase.json');

        $defaults = new \stdClass();
        $defaults->path = null;

        if (file_exists($configPath)) {
            foreach (json_decode(file_get_contents($configPath)) as $property => $value) {
                $defaults->{$property} = $value;
            }
        }

        return $defaults;
    }

    /**
     * Get a Flatbase object for a given storage path.
     *
     * @return Flatbase
     */
    protected function getFlatbase($storagePath)
    {
        $factory = $this->factory;

        return $factory ? $factory($storagePath) : new Flatbase(new Filesystem($storagePath));
    }

    /**
     * Testing method
     *
     * @param callable $factory
     */
    public function setFlatbaseFactory(callable $factory)
    {
        $this->factory = $factory;
    }
}
