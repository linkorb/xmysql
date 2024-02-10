<?php

namespace XMySQL\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputOption;
use XMySQL\XMySQL;
use Symfony\Component\Process\Process;
use RuntimeException;

class ShowCommand extends Command
{
    protected function configure()
    {
        $this
            ->setName('show')
            ->setDescription('Show config')
            ->addOption(
                'server',
                's',
                InputOption::VALUE_IS_ARRAY|InputOption::VALUE_REQUIRED,
                'Servers'
            )
            // ->addOption(
            //     'limit',
            //     'l',
            //     InputOption::VALUE_IS_ARRAY|InputOption::VALUE_REQUIRED,
            //     'Limit repos to matches'
            // )
        ;
    }

    public function execute(InputInterface $input, OutputInterface $output)
    {
        $xMySQL = XMySQL::fromEnv();
        $servers = $xMySQL->getServers();

        $output->writeLn('<comment>Backup path:</comment> ' . $xMySQL->getBackupPath());
        
        foreach ($servers as $server) {
            $output->writeLn('<info>' . $server->getName() . '</info>: mysql://' . $server->getUsername() . ':***@' . $server->getHost());
            $dbs = $server->getDatabases();
            $first = true;
            foreach ($dbs as $dbName => $db) {
                if (!$first) {
                    $output->write(', ');
                }
                $output->write($dbName);
                $first = false;
            }
            $output->writeLn('');
        }

        return 0;
    }
}
