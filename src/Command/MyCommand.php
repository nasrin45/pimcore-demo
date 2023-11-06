<?php

namespace App\Command;

use Pimcore\Console\AbstractCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class MyCommand extends AbstractCommand
{
    protected function configure(): void
    {
        $this
            ->setName('custom:command')
            ->setDescription('Custom command');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
//        // dump
//        $this->dump("Isn't that custom?");
//
//        // add newlines through flags
//        $this->dump("Dump #2");
//
//        // only dump in verbose mode
//        $this->dumpVerbose("Dump verbose");
//
//        // Output as white text on red background.
//        $this->writeError('oh noes!');

        // Output as green text.
//        $this->writeInfo('Custom command');

//        // Output as blue text.
//        $this->writeComment('comment');
//
//        // Output as yellow text.
//        $this->writeQuestion('question');
        try {
            // Your command logic goes here
            $output->writeln('<info>Custom command executed successfully!</info>');
            return Command::SUCCESS; // 0 for success
        } catch (\Exception $e) {
            // Handle exceptions or errors
            $output->writeln('Error: ' . $e->getMessage());
            return Command::FAILURE; // non-zero for errors
        }    }
}
