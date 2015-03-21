<?php

namespace Flatbase\Console\Commands;

use Flatbase\FlatbaseTestCase;
use Flatbase\Query\InsertQuery;
use Prophecy\Argument;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\ArrayInput;

class InsertCommandTest extends FlatbaseTestCase
{
    public function testInstance()
    {
        $command = new InsertCommand();
        $this->assertTrue($command instanceof Command);
    }

    public function testRequiredArguments()
    {
        $this->runReadCommandTest(['collection' => 'users', 'set' => ['name=Adam']], function(InsertQuery $query) {
                $this->assertEquals($query->getCollection(), 'users');
                $this->assertEquals($query->getValues(), ['name' => 'Adam']);
                return true;
        });
    }

    public function testSetAcceptsMupltipleArguments()
    {
        $this->runReadCommandTest(['collection' => 'users', 'set' => ['name=Adam', 'dob=1990-08-08', 'from=UK']], function(InsertQuery $query) {
                $this->assertEquals($query->getCollection(), 'users');
                $this->assertEquals($query->getValues(), [
                        'name' => 'Adam',
                        'dob' => '1990-08-08',
                        'from' => 'UK'
                    ]);
                return true;
        });
    }

    protected function runReadCommandTest(array $input, callable $executeArgumentExpectation)
    {
        // Create the command
        $command = new InsertCommand();

        // Attach a Flatbase double to the command
        $flatbase = $this->prophesize('Flatbase\Flatbase');
        $command->setFlatbaseFactory(function($storageDir) use ($flatbase) {
                return $flatbase->reveal();
            });

        // Expect read() to be called and return a ReadQuery. Making sure the ReadQuery uses our Flatbase double
        $flatbase->insert()->shouldBeCalled()->willReturn($query = new InsertQuery());
        $query->setFlatbase($flatbase->reveal());

        // Define what the query argument should look like when Flatbase::execute() is called
        $expectedQuery = Argument::that($executeArgumentExpectation);
        $flatbase->execute($expectedQuery)->shouldBeCalled();

        // Run it
        $input = new ArrayInput($input);
        $output = $this->prophesize('Symfony\Component\Console\Output\OutputInterface');
        $command->run($input, $output->reveal());
    }
}