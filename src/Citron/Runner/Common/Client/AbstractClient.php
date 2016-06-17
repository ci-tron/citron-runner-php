<?php
/**
 * This file is a part of a nekland library
 *
 * (c) Nekland <nekland.fr@gmail.fr>
 *
 * For the full license, take a look to the LICENSE file
 * on the root directory of this project
 */

namespace Citron\Runner\Common\Client;


use Ratchet\Client\WebSocket;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Process\Process;

abstract class AbstractClient
{
    /**
     * @var WebSocket
     */
    protected $connection;

    /**
     * @var OutputInterface
     */
    protected $output;

    public function __construct(WebSocket $connection, OutputInterface $output)
    {
        $this->connection = $connection;
        $this->connection->on('message', [$this, 'onMessage']);
        $this->output = $output;

        $connection->send('RUNNER:init:{"type": "simple"}');
    }

    public function onMessage(string $message)
    {
        $message = MessageFactory::createMessage($message);

        switch ($message->getAction()) {
            case 'run':
                $this->output->writeln('<info>Start new build</info>');
                $this->run($message->getContent()['repo'], $message->getContent()['script']);
                break;
            case 'shutdown':
                $this->connection->close();
                break;
        }
    }

    public abstract function run(string $repo, string $script);
}
