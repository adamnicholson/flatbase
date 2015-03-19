<?php

namespace Flatbase\Console\Commands;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\ConsoleOutput;
use Symfony\Component\Console\Output\NullOutput;

class ReadCommandTest extends \PHPUnit_Framework_TestCase
{
    public function testInstance()
    {
        $command = new ReadCommand();
        $this->assertTrue($command instanceof Command);
    }

    public function testReadUsesCorrectCollection()
    {
        $command = new ReadCommand();
        $input = new ArrayInput([
            'collection' => 'users'
        ]);

        $output= new NullOutput();
        $command->run($input, $output);
        // @TODO Finish
    }
}