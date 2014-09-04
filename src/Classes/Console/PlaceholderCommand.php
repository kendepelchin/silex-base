<?php

namespace Classes\Console;

use Knp\Command\Command;
use Symfony\Component\Console\Input\InputDefinition;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

use Classes\Console\Console;
use Classes\Utils\Timing;

/**
 * Placeholder command
 *
 * @extends Knp\Command\Command
 * @author  Ken Depelchin <ken.depelchin@gmail.com>
 */
class PlaceholderCommand extends Command {

    protected function configure() {
        $config = Console::getConfig(Console::PLACEHOLDER);

        $this->interval = $config['interval'] ? $config['interval'] : Console::DEFAULT_INTERVAL;

        $this
            ->setName($config['name'])
            ->setDescription($config['description'])
            ->addOption(
               'debug',
               null,
               InputOption::VALUE_NONE,
               'If set, the task will run in debug mode'
            )
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output) {
        $app = $this->getSilexApplication();
        $timing = new Timing();

        while (true) {
            $timing->start();
            $output->writeln("[PlaceholderCommand] " . $timing->getNowFormatted());

            if ($input->getOption('debug')) {
                $output->writeln("<info>[Running in debug mode]</info>\n");
            }

            $output->writeln("Successfully running the placeholder script.");

            sleep($this->interval);

            $timing->stop();

            // need to be called manually?
            $output->writeln($timing->__toString());
        }
    }
}
