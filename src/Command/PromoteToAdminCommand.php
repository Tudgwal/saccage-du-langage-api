<?php

namespace App\Command;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Console\Input\ArrayInput;

#[AsCommand(
    name: 'promote-to-admin',
    description: 'Promote User to Admin role',
)]
class PromoteToAdminCommand extends Command
{

    public function __contruct()
    {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addArgument('email', InputArgument::OPTIONAL, 'User email')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $email = $input->getArgument('email');

        $command = new ArrayInput([
            'command' => 'doctrine:query:sql',
            'sql' => 'UPDATE user SET roles = \'["ROLE_USER", "ROLE_ADMIN"]\' WHERE email = \'' . $email . '\'',
        ]);

        $this->getApplication()->doRun($command, $output);

        return Command::SUCCESS;
    }
}
