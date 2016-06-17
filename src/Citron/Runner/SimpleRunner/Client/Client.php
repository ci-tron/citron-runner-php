<?php
/**
 * This file is a part of a nekland library
 *
 * (c) Nekland <nekland.fr@gmail.fr>
 *
 * For the full license, take a look to the LICENSE file
 * on the root directory of this project
 */

namespace Citron\Runner\SimpleRunner\Client;


use Citron\Runner\Common\Client\AbstractClient;
use Citron\Runner\Common\Client\MessageFactory;
use Ratchet\Client\WebSocket;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Process\Process;

class Client extends AbstractClient
{
    public function run(string $repo, array $script)
    {
        $this->output->writeln('<info>Running...</info>');
        $filesystem = new Filesystem();
        $workingDir = sys_get_temp_dir() . '/nekland_ci';

        if ($filesystem->exists($workingDir)) {
            $filesystem->remove($workingDir);
        }

        $filesystem->mkdir($workingDir);
        chdir($workingDir);

        $process = new Process('git clone ' . $repo . ' NeklandCiTestFolder');
        $process->run([$this, 'onRunningProcess']);
        $process->wait([$this, 'onRunningProcess']);
        chdir($workingDir . '/NeklandCiTestFolder');


        $success = true;
        foreach ($script as $process) {
            $this->output->writeln('<info>'.$process.'</info>');
            $process = new Process($process);
            $process->run([$this, 'onRunningProcess']);
            $process->wait([$this, 'onRunningProcess']);
            
            if (!$process->isSuccessful()) {
                $success = false;
                break;
            }
        }

        $this->connection->send('RUNNER:process:{"finished": true, "success": ' . ($success ? 'true': 'false') . '}');
        $this->output->writeln('<info>Process done !</info>');
    }

    public function onRunningProcess($type, $buffer)
    {
        $this->connection->send('RUNNER:process:{"finished": false, "log": "'.addslashes($buffer).'"}');
    }
}
