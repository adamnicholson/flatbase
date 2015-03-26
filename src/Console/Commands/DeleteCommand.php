<?php

namespace Flatbase\Console\Commands;

use Flatbase\Exception\InvalidArgumentException;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class DeleteCommand extends AbstractCommand
{
    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->input = $input;
        $this->output = $output;

        $this->buildQuery($input)->execute();

        // Write out the count
        $output->writeln('<info>Delete query executed</info>');
    }

    /**
     * @param InputInterface $input
     * @return \Flatbase\Query\DeleteQuery
     */
    protected function buildQuery(InputInterface $input)
    {
        $flatbase = $this->getFlatbase($this->getStoragePath());
        $query = $flatbase->delete()->in($input->getArgument('collection'));

        foreach ($this->input->getOption('where') as $where) {
            $splode = explode(',', $where);

            if (count($splode) !== 3) {
                throw new InvalidArgumentException('Each --where must be passed a string in the format "{key},{operator},{value}. Eg. --where "name,==,Adam"');
            }

            list($l, $op, $r) = $splode;

            $query->where($l, $op, $r);
        }

        return $query;
    }

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('delete')
            ->setDescription('Delete into a collection')
            ->addArgument(
                'collection',
                InputArgument::REQUIRED,
                'Name of the collection to delete from'
            )
            ->addOption(
                'where',
                null,
                InputOption::VALUE_IS_ARRAY | InputOption::VALUE_OPTIONAL,
                'Add a "where" statement. Must include 3 comma-separated parts. Eg. "name,==,Adam"',
                []
            )
        ;

        parent::configure();
    }
}
