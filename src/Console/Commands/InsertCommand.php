<?php

namespace Flatbase\Console\Commands;

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

class InsertCommand extends AbstractCommand
{
    /**
     * @var ClonerInterface
     */
    protected $cloner;
    /**
     * @var DumperInterface
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

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->input = $input;
        $this->output = $output;

        $this->buildQuery($input)->execute();

        // Write out the count
        $output->writeln('Delete query executed');
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
                throw new \InvalidArgumentException('--set can only be passed a string with a single equals sign formatted as "key=value"');
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
                'Name of the collection to insert into from'
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
