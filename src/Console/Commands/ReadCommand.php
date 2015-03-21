<?php

namespace Flatbase\Console\Commands;

use Flatbase\Console\Dumper;
use Flatbase\Flatbase;
use Flatbase\Storage\Filesystem;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\VarDumper\Cloner\ClonerInterface;
use Symfony\Component\VarDumper\Cloner\DumperInterface;
use Symfony\Component\VarDumper\Cloner\VarCloner;
use Symfony\Component\VarDumper\Dumper\CliDumper;

class ReadCommand extends AbstractCommand
{
    /**
     * @var VarCloner
     */
    protected $cloner;
    /**
     * @var CliDumper
     */
    protected $dumper;
    /**
     * @var InputInterface
     */
    protected $input;
    /**
     * @var OutputInterface
     */
    protected $output;
    /**
     * @var callable
     */
    protected $factory;

    public function __construct(ClonerInterface $cloner, DumperInterface $dumper)
    {
        parent::__construct();

        $this->cloner = $cloner;
        $this->dumper = $dumper;
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

        $this->dumper->setOutputInterface($output);

        // Fetch the records
        $records = $this->buildQuery($input)->get();

        // Write out the count
        $output->writeln('Found ' . $records->count() . ' records');

        if ($input->getOption('count')) {
            return;
        }

        foreach ($records as $record) {
            $this->dumper->dump($this->cloner->cloneVar($record));
        }
    }

    /**
     * @param InputInterface $input
     * @return \Flatbase\Query\ReadQuery
     */
    private function buildQuery(InputInterface $input)
    {
        $flatbase = $this->getFlatbase($this->getStoragePath());

        $query = $flatbase->read()->in($input->getArgument('collection'));

        foreach ($this->input->getOption('where') as $where) {
            list($l, $op, $r) = explode(',', $where);

            $query->where($l, $op, $r);
        }

        if ($limit = $this->input->getOption('limit')) {
            $query->limit($limit);
        }

        if ($offset = $this->input->getOption('skip')) {
            $query->skip($offset);
        }

        if ($offset = $this->input->getOption('sort')) {
            $query->sort($offset);
        }

        if ($offset = $this->input->getOption('sortDesc')) {
            $query->sortDesc($offset);
        }

        if ($this->input->getOption('first')) {
            $query->limit(1);
        }

        return $query;
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
                'The collection to use'
            )
            ->addOption(
                'where',
                null,
                InputOption::VALUE_IS_ARRAY | InputOption::VALUE_OPTIONAL,
                'Add a "where" statement. Must include 3 comma-separated parts. Eg. "name,==,Adam"',
                []
            )
            ->addOption(
                'limit',
                null,
                InputOption::VALUE_OPTIONAL,
                'Limit the number of results'
            )
            ->addOption(
                'skip',
                null,
                InputOption::VALUE_OPTIONAL,
                'Skip the first x number of results'
            )
            ->addOption(
                'sort',
                null,
                InputOption::VALUE_OPTIONAL,
                'Sort the results by a field in ascending order'
            )
            ->addOption(
                'sortDesc',
                null,
                InputOption::VALUE_OPTIONAL,
                'Sort the results by a field in descending order'
            )
            ->addOption(
                'count',
                null,
                InputOption::VALUE_NONE,
                'Only get the record count'
            )
            ->addOption(
                'first',
                null,
                InputOption::VALUE_NONE,
                'Only get the first record'
            )
        ;

        parent::configure();
    }

    /**
     * Testing method
     *
     * @return Flatbase
     */
    private function getFlatbase($storagePath)
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
