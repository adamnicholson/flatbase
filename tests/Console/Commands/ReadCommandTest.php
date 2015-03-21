<?php

namespace Flatbase\Console\Commands;

use Flatbase\Collection;
use Flatbase\Console\Dumper;
use Flatbase\FlatbaseTestCase;
use Flatbase\Query\ReadQuery;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\BufferedOutput;
use Symfony\Component\Console\Output\ConsoleOutput;
use Symfony\Component\Console\Output\NullOutput;
use Symfony\Component\VarDumper\Cloner\VarCloner;

class ReadCommandTest extends FlatbaseTestCase
{
    public function testInstance()
    {
        $command = new ReadCommand(new VarCloner(), new Dumper());
        $this->assertTrue($command instanceof Command);
    }

    public function testReadUsesCorrectCollection()
    {
        $command = new ReadCommand(new VarCloner(), new Dumper());

        $flatbase = $this->prophesize('Flatbase\Flatbase');
        $command->setFlatbaseFactory(function($storageDir) use ($flatbase) {
            return $flatbase->reveal();
        });

        $flatbase->read()->willReturn($query = new ReadQuery());
        $query->setFlatbase($flatbase->reveal());
        $flatbase->execute($query)->shouldBeCalled()->willReturn(new Collection());
        $input = new ArrayInput([
            'collection' => 'users'
        ]);

        $output = $this->prophesize('Symfony\Component\Console\Output\OutputInterface');
        $command->run($input, $output->reveal());
    }
}