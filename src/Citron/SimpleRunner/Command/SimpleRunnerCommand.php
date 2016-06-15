<?php

namespace Citron\SimpleRunner\Command;

use Citron\SimpleRunner\Client\Client;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * This file is a part of a nekland library
 *
 * (c) Nekland <nekland.fr@gmail.fr>
 *
 * For the full license, take a look to the LICENSE file
 * on the root directory of this project
 */
class SimpleRunnerCommand extends Command
{
    protected function configure()
    {
        $this
            ->setName('citron:simple_runner:start')
            ->setDescription('Starts the simple runner of Nekland CI.')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        \Ratchet\Client\connect('ws://127.0.0.1:8282/runner')->then(function($conn) use ($output) {
            $client = new Client($conn, $output);

        }, function ($e) {
            echo "Could not connect: {$e->getMessage()}\n";
        });

        /*
        $loop = \React\EventLoop\Factory::create();
        $client = new Client(new CacheStorage($this->getApplication()->getRootDir() . '/cache'), $input, $output, $this->getHelper('question'));

        new WebSocketClient(
            $client,
            $loop,
            '127.0.0.1',
            8282,
            '/runner',
            '127.0.0.1'
        );

        $loop->run();
        */
    }
}

