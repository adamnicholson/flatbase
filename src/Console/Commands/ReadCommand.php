<?php

namespace Flatbase\Console\Commands;

use Flatbase\Console\Dumper;
use Flatbase\Exception\InvalidArgumentException;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\VarDumper\Cloner\ClonerInterface;

class ReadCommand extends AbstractCommand
{
    /**
     * @var ClonerInterface
     */
    protected $cloner;
    /**
     * @var \Flatbase\Console\Dumper
     */
    protected $dumper;

    public function __construct(ClonerInterface $cloner, Dumper $dumper)
    {
        parent::__construct();

        $this->cloner = $cloner;
        $this->dumper = $dumper;
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->input = $input;
        $this->output = $output;

        $this->dumper->setOutputInterface($output);

        // Fetch the records
        $records = $this->buildQuery($input)->get();

        // Write out the count
        $output->writeln('<info>Found '.$records->count().' records</info>');

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
    protected function buildQuery(InputInterface $input)
    {
        $flatbase = $this->getFlatbase($this->getStoragePath());
        $query = $flatbase->read()->in($input->getArgument('collection'));

        foreach ($this->input->getOption('where') as $where) {
            $splode = explode(',', $where);

            if (count($splode) !== 3) {
                throw new InvalidArgumentException('Each --where must be passed a string in the format "{key},{operator},{value}. Eg. --where "name,==,Adam"');
            }

            list($l, $op, $r) = $splode;

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
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('read')
            ->setDescription('Read from a collection')
            ->addArgument(
                'collection',
                InputArgument::REQUIRED,
                'Name of the collection to read from'
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
}
