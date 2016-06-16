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


use Citron\Runner\Common\Client\MessageFactory;
use Ratchet\Client\WebSocket;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Process\Process;

class Client
{
    /**
     * @var WebSocket
     */
    private $connection;

    /**
     * @var OutputInterface
     */
    private $output;

    public function __construct(WebSocket $connection, OutputInterface $output)
    {
        $this->connection = $connection;
        $this->connection->on('message', [$this, 'onMessage']);
        $this->output = $output;

        $connection->send('RUNNER:init:');
    }

    public function onMessage(string $message)
    {
        $message = MessageFactory::createMessage($message);

        switch ($message->getAction()) {
            case 'run':
                $this->run($message->getContent()['repo'], $message->getContent()['script']);
                break;
            case 'shutdown':
                $this->connection->close();
                break;
        }
    }

    public function run(string $repo, string $script)
    {
        $filesystem = new Filesystem();
        $workingDir = sys_get_temp_dir() . '/nekland_ci';

        if ($filesystem->exists($workingDir)) {
            $filesystem->remove($workingDir);
        }

        $filesystem->mkdir($workingDir);
        chdir($workingDir);

        $process = new Process('git clone ' . $repo . ' NeklandCiTestFolder');
        $process->run([$this, 'onRunningProcess']);
        chdir($workingDir . '/NeklandCiTestFolder');

        $processes = explode("\n", $script);
        array_map('trim', $processes);

        foreach ($processes as $process) {
            $process = new Process($process);
            $process->run([$this, 'onRunningProcess']);
        }

        $this->connection->emit('RUNNER:process:{"finished": true}');
    }

    public function onRunningProcess($type, $buffer)
    {
        if (Process::ERR === $type) {
            $this->output->writeln('<danger>ERR: ' . $buffer . '</danger>');
            return;
        }
        $this->connection->emit('RUNNER:process:{"finished": false, "content": "'.addslashes($buffer).'"}');
    }
}
