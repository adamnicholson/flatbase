<?php

namespace Flatbase\Console\Commands;

use Flatbase\Flatbase;
use Flatbase\Storage\Filesystem;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
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

    public function __construct()
    {
        parent::__construct();

        $this->cloner = new VarCloner();
        $this->dumper = new CliDumper();
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
        $flatbase = new Flatbase(new Filesystem($this->getStoragePath()));

        $query = $flatbase->read()->in($input->getArgument('collection'));

        foreach ($this->input->getOption('where') as $where) {
            list($l, $op, $r) = explode(',', $where);

            $query->where($l, $op, $r);
        }

        return $query;
    }

    /**
     * Configures the current command.
     */
    protected function configure()
    {
        parent::configure();

        $this
            ->setName('read')
            ->setDescription('Read from a collection')
            ->addArgument(
                'collection',
                InputArgument::REQUIRED,
                'The collection to use'
            )
            ->addOption(
                'count',
                null,
                InputOption::VALUE_NONE,
                'If set, only the record count will be output'
            )
            ->addOption(
                'where',
                null,
                InputOption::VALUE_IS_ARRAY | InputOption::VALUE_OPTIONAL,
                'Where',
                []
            )
        ;
    }
}
