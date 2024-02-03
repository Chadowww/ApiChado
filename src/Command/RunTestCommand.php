<?php

namespace App\Command;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\Process\Process;

#[AsCommand(
    name: 'app:run-tests',
    description: 'Run all tests',
)]
class RunTestCommand extends Command
{
    private string $projectDir;

    public function __construct(KernelInterface $kernel)
    {
        $this->projectDir = $kernel->getProjectDir();

        parent::__construct();
    }

    protected function configure(): void
    {
//        $this
//            ->addArgument('arg1', InputArgument::OPTIONAL, 'Argument description')
//            ->addOption('option1', null, InputOption::VALUE_NONE, 'Option description')
//        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $phpunitProcess = new Process(['./bin/phpunit'], $this->projectDir);
        $phpunitProcess->run(function ($type, $buffer) use ($io) {
            $io->write($buffer);
        });

        if (!$phpunitProcess->isSuccessful()) {
            $io->error('Tests failed!');
            return Command::FAILURE;
        }

        $io->success('Tests passed successfully.');

        return Command::SUCCESS;
    }
}
