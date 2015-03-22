<?php

namespace Flatbase\Console;

use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\VarDumper\Dumper\CliDumper;

class Dumper extends CliDumper
{
    /**
     * @var OutputInterface
     */
    private $outputInterface;

    public function setOutputInterface(OutputInterface $outputInterface)
    {
        $this->outputInterface = $outputInterface;
    }

    /**
     * Generic line dumper callback.
     *
     * @param string $line  The line to write.
     * @param int    $depth The recursive depth in the dumped structure.
     */
    protected function echoLine($line, $depth, $indentPad)
    {
        if (-1 !== $depth) {
            $this->outputInterface->writeln(str_repeat($indentPad, $depth).$line);
        }
    }
}
