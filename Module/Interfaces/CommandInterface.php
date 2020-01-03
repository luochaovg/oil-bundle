<?php

namespace Leon\BswBundle\Module\Interfaces;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

interface CommandInterface
{
    /**
     * @return array
     */
    public function args(): array;

    /**
     * @return array
     */
    public function base(): array;

    /**
     * @param InputInterface  $input
     * @param OutputInterface $output
     *
     * @return mixed
     */
    public function execute(InputInterface $input, OutputInterface $output);
}