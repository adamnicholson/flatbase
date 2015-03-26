<?php

namespace Flatbase\Console\Commands;

use Flatbase\Exception\InvalidArgumentException;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class UpdateCommand extends AbstractCommand
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
        $output->writeln('<info>Update query executed</info>');
    }

    /**
     * @param InputInterface $input
     * @return \Flatbase\Query\UpdateQuery
     */
    protected function buildQuery(InputInterface $input)
    {
        $flatbase = $this->getFlatbase($this->getStoragePath());
        $query = $flatbase->update()->in($input->getArgument('collection'));

        // Parse new values to set/update
        $values = [];
        foreach ($input->getArgument('set') as $value) {
            $splode = explode('=', $value);
            if (count($splode) !== 2) {
                throw new \InvalidArgumentException('Each value set must be passed a string with a single key-value pair formatted as "key=value". Eg. "flatbase update users name=Adam created=Monday age=25"');
            }
            $values[$splode[0]] = $splode[1];
        }
        $query->setValues($values);

        // Parse "where" clauses
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
            ->setName('update')
            ->setDescription('Update into a collection')
            ->addArgument(
                'collection',
                InputArgument::REQUIRED,
                'Name of the collection to update into'
            )
            ->addArgument(
                'set',
                InputArgument::REQUIRED | InputArgument::IS_ARRAY,
                'Set a value to update. Must include a key value pair separated by "=" (eg. "name=Adam")'
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
