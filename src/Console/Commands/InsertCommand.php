<?php

namespace Flatbase\Console\Commands;

use Flatbase\Exception\InvalidArgumentException;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class InsertCommand extends AbstractCommand
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
        $output->writeln('<info>Insert query executed</info>');
    }

    /**
     * @param InputInterface $input
     * @return \Flatbase\Query\InsertQuery
     */
    protected function buildQuery(InputInterface $input)
    {
        $flatbase = $this->getFlatbase($this->getStoragePath());
        $query = $flatbase->insert()->in($input->getArgument('collection'));

        $values = [];
        foreach ($input->getArgument('set') as $value) {
            $splode = explode('=', $value);
            if (count($splode) !== 2) {
                throw new InvalidArgumentException('Each value set must be passed a string with a single key-value pair formatted as "key=value". Eg. "flatbase insert users name=Adam created=Monday age=25"');
            }
            $values[$splode[0]] = $splode[1];
        }
        $query->setValues($values);

        return $query;
    }

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('insert')
            ->setDescription('Insert into a collection')
            ->addArgument(
                'collection',
                InputArgument::REQUIRED,
                'Name of the collection to insert into'
            )
            ->addArgument(
                'set',
                InputArgument::REQUIRED | InputArgument::IS_ARRAY,
                'Set a value for the new record. Must include a key value pair separated by "=" (eg. "name=Adam")'
            )
        ;

        parent::configure();
    }
}
