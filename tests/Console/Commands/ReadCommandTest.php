<?php

namespace Flatbase\Console\Commands;

use Flatbase\Collection;
use Flatbase\Console\Dumper;
use Flatbase\FlatbaseTestCase;
use Flatbase\Query\ReadQuery;
use Prophecy\Argument;
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
        $this->runReadCommandTest(['collection' => 'users'], function(ReadQuery $query) {
                $this->assertEquals($query->getCollection(), 'users');
                return true;
        });
    }

    public function testReadWheres()
    {
        $this->runReadCommandTest(['collection' => 'users', '--where' => ['foo,==,bar']], function(ReadQuery $query) {
                $this->assertEquals($query->getCollection(), 'users');
                $wheres = $query->getConditions();
                $this->assertEquals(count($wheres), 1);
                $this->assertEquals($wheres[0], ['foo', '==', 'bar']);
                return true;
        });
    }

    protected function runReadCommandTest(array $input, callable $executeArgumentExpectation)
    {
        // Create the command
        $command = new ReadCommand(new VarCloner(), new Dumper());

        // Attach a Flatbase double to the command
        $flatbase = $this->prophesize('Flatbase\Flatbase');
        $command->setFlatbaseFactory(function($storageDir) use ($flatbase) {
                return $flatbase->reveal();
            });

        // Expect read() to be called and return a ReadQuery. Making sure the ReadQuery uses our Flatbase double
        $flatbase->read()->shouldBeCalled()->willReturn($query = new ReadQuery());
        $query->setFlatbase($flatbase->reveal());

        // Define what the query argument should look like when Flatbase::execute() is called
        $expectedQuery = Argument::that($executeArgumentExpectation);

        $flatbase->execute($expectedQuery)->shouldBeCalled()->willReturn(new Collection());
        $input = new ArrayInput($input);

        $output = $this->prophesize('Symfony\Component\Console\Output\OutputInterface');
        $command->run($input, $output->reveal());
    }
}