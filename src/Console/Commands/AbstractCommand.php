<?php

namespace Flatbase\Console\Commands;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputOption;

abstract class AbstractCommand extends Command
{
    /**
     * Configures the current command.
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

    protected function getStoragePath()
    {
        if ($override = $this->input->getOption('path')) {
            return $override;
        }
        $config = $this->getConfig();

        return getcwd() . '/' . $config->path;
    }

    /**
     * Get the config data
     *
     * @return \stdClass
     */
    protected function getConfig()
    {
        $configPath = $this->input->getOption('config') ?: (getcwd() . '/flatbase.json');

        $defaults = new \stdClass();
        $defaults->path = null;

        if (file_exists($configPath)) {
            foreach (json_decode(file_get_contents($configPath)) as $property => $value) {
                $defaults->{$property} = $value;
            }
        }

        return $defaults;
    }
}
