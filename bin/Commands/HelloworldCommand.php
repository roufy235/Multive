<?php
namespace MultiveCLI;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class HelloworldCommand extends Command {

    protected function configure () : void {
        parent::configure(); // TODO: Change the autogenerated stub
        $this->setName('HelloWorld')
            ->setDescription('Prints Hello World!')
            ->setHelp("Demonstration of custom commands created by Symfony Console component.")
            ->addArgument('username', InputArgument::REQUIRED, 'Pass your username');
    }


    protected function execute (InputInterface $input, OutputInterface $output) : int {
        $output->writeln(sprintf('Hello World!, %s', $input->getArgument('username')));
        return 0;
    }

}