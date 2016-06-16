<?php

namespace Citron\Runner;

use Citron\Runner\SimpleRunner\Command\SimpleRunnerCommand;
use Symfony\Component\Console\Application;

/**
 * This file is a part of a nekland library
 *
 * (c) Nekland <nekland.fr@gmail.fr>
 *
 * For the full license, take a look to the LICENSE file
 * on the root directory of this project
 */
class RunnerApplication extends Application
{
    private $workingDirectory;

    public function __construct($workingDirectory)
    {
        $this->workingDirectory = $workingDirectory;
        parent::__construct();
    }

    /**
     * Register commands
     *
     * @return array|\Symfony\Component\Console\Command\Command[]
     */
    protected function getDefaultCommands()
    {
        $defaultCommands = parent::getDefaultCommands();

        $defaultCommands[] = new SimpleRunnerCommand();

        return $defaultCommands;
    }

    public function getRootDir()
    {
        return $this->workingDirectory;
    }
}
