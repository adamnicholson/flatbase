<?php

namespace Flatbase\Console\Commands;

use Flatbase\Flatbase;
use Flatbase\Storage\Filesystem;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\VarDumper\Cloner\VarCloner;
use Symfony\Component\VarDumper\Dumper\CliDumper;
use Symfony\Component\VarDumper\VarDumper;

class ReadCommand extends Command
{
    private $dumper;
    /**
     * @var InputInterface
     */
    private $input;
    /**
     * @var OutputInterface
     */
    private $output;

    public function __construct()
    {
        parent::__construct();

        $this->dumper = new VarDumper();
    }

    /**
     * Executes the current command.
     *
     * This method is not abstract because you can use this class
     * as a concrete class. In this case, instead of defining the
     * execute() method, you set the code to execute by passing
     * a Closure to the setCode() method.
     *
     * @param InputInterface  $input  An InputInterface instance
     * @param OutputInterface $output An OutputInterface instance
     *
     * @return null|int null or 0 if everything went fine, or an error code
     *
     * @throws \LogicException When this abstract method is not implemented
     *
     * @see setCode()
     * @todo Write CliDumper output to OutputInterface rather than globally
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->input = $input;
        $this->output = $output;

        $cloner = new VarCloner();
        $dumper = new CliDumper();

        // Fetch the records
        $records = $this->buildQuery($input)->get();

        // Write out the count
        $output->writeln('Found ' . $records->count() . ' records');

        if ($input->getOption('count')) {
            return;
        }

        foreach ($records as $record) {
            $dumper->dump($cloner->cloneVar($record));
        }
    }

    /**
     * @param InputInterface $input
     * @return \Flatbase\Query\ReadQuery
     */
    private function buildQuery(InputInterface $input)
    {
        $flatbase = new Flatbase(new Filesystem($this->getStoragePath()));

        return $flatbase->read()
            ->in($input->getArgument('collection'));
    }

    /**
     * Configures the current command.
     */
    protected function configure()
    {
        $this
            ->setName('read')
            ->setDescription('Read from a collection')
            ->addArgument(
                'collection',
                InputArgument::REQUIRED,
                'The collection to read from'
            )
            ->addOption(
                'count',
                null,
                InputOption::VALUE_NONE,
                'If set, only the record count will be output'
            )
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

    private function getStoragePath()
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
    private function getConfig()
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
