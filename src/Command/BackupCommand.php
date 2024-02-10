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

class BackupCommand extends Command
{
    protected function configure()
    {
        $this
            ->setName('backup')
            ->setDescription('Backup')
            ->addOption(
                'server',
                's',
                InputOption::VALUE_IS_ARRAY|InputOption::VALUE_REQUIRED,
                'Servers'
            )
        ;
    }

    public function execute(InputInterface $input, OutputInterface $output)
    {
        $xMySQL = XMySQL::fromEnv();
        $servers = $xMySQL->getServers();

        foreach ($servers as $server) {
            $output->writeLn('<info>' . $server->getName() . '</info>: mysql://' . $server->getUsername() . ':***@' . $server->getHost());
            $dbs = $server->getDatabases();

            $first = true;
            foreach ($dbs as $dbName => $db) {
                $output->writeLn($dbName);

                $outputFilename = $xMySQL->getBackupPath() . '/' . $server->getName() . '/' . $dbName . '.sql.gz';

                $cmd = '';
                $cmd .= 'mysqldump ';
                $cmd .= '--skip-dump-date ';
                $cmd .= '--single-transaction ';
                $cmd .= '--triggers ';
                $cmd .= '--opt ';
                $cmd .= '--routines ';
                // $cmd .= '--column-statistics=0 ';
                $cmd .= ' -h ' . $server->getHost() . ' -u ' . $server->getUsername() . ' ' . $dbName;
                $cmd .= '| gzip -n ';
                $cmd .= ' > ' . $outputFilename;
                echo $cmd . PHP_EOL;
    
                $output->writeLn('Archiving ' . $dbName . ' to ' . $outputFilename);
    
                $process = Process::fromShellCommandline($cmd);
                $process->setTimeout(60 * 10); // 10 minutes
                // $process->setWorkingDirectory($repo->getPath());
                $process->mustRun(
                    null,
                    [
                        'MYSQL_PWD' => $server->getPassword(),
                    ]
                );
            }
        }

        return 0;
    }
}
