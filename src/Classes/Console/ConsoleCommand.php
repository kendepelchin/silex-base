<?php

namespace Classes\Console;

use Knp\Command\Command;
use Symfony\Component\Console\Input\InputDefinition;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

use Classes\Console\Console;
use Classes\Utils\File;
use Classes\Utils\Timing;

/**
 * This command fetches all scripts and runs them
 *
 * @extends Knp\Command\Command
 * @author  Ken Depelchin <ken.depelchin@gmail.com>
 */
class ConsoleCommand extends Command {

    protected function configure() {
        $this
            ->setName('scripts')
            ->setDescription('Start all the console scripts.')
            ->addOption(
               'debug',
               null,
               InputOption::VALUE_NONE,
               'If set, the task will run in debug mode.'
            )
            ->addOption(
                'kill',
                null,
                InputOption::VALUE_NONE,
                'Kill and restart all scripts.'
            )
            ->addOption(
                'list',
                null,
                InputOption::VALUE_NONE,
                'Which scripts are running?'
            )
            ->addOption(
                'l',
                null,
                InputOption::VALUE_NONE,
                '[alias] Which scripts are running?'
            )
            ->addArgument(
                'script',
                InputArgument::OPTIONAL,
                'Select one script to kill or to start.'
            )
        ;
  }

    protected function execute(InputInterface $input, OutputInterface $output) {
        $app = $this->getSilexApplication();
        $timing = new Timing();

        $timing->start();
        $output->writeln("[ConsoleCommand] " . $timing->getNowFormatted());

        if ($input->getArgument('script')) {
            $script = $input->getArgument('script');

            if (Console::isRunning($script) && $input->getOption('kill')) {
                $killed = Console::kill($script);
                if ($killed) {
                    $output->writeln("<comment>Killed script $script</comment>");
                }
                else {
                   $output->writeln("<error>Error killing script $script. (already killed?)</error>");
                }
            }
            elseif (!$input->getOption('kill')) {
                $run = Console::run($script);
                if ($run) {
                   $output->writeln("<comment>Starting $script</comment>");
                }
                else {
                    $output->writeln("Error running script <error>$script</error> (config not present? already running?)");
                }
            }
            else {
                $output->writeln("<error>Script is not running. Start it: app/console/console start $script</error> ");
            }
        }
        else {
            $files = File::readDirectory(__DIR__ . '/../../../app/console');
            if (!empty($files) && count($files) != 1) {
                $output->writeln("<info>Found " . count($files) . " console scripts which need to run.</info>\n");
                foreach ($files as $script) {
                    if (Console::isValidScript($script)) {
                        if ($input->getOption('list') || $input->getOption('l')) {
                            $output->writeln("$script: " . (Console::isRunning($script) ? '<info>running</info>' : '<error>not running</error>'));
                        }
                        else {
                            // if we need to kill (and debug is not active), kill the script
                            if ($input->getOption('kill') && !$input->getOption('debug')) {
                                $killed = Console::kill($script);
                                if ($killed) {
                                    $output->writeln("<comment>Killed script $script</comment>");
                                }
                                else {
                                   $output->writeln("<error>Error killing script $script. (already killed?)</error>");
                                }
                            }
                            else {
                                $run = Console::run($script);
                                if ($run) {
                                    $output->writeln("<comment>Starting $script</comment>");
                                }
                                else {
                                    $output->writeln("Error running script <error>$script</error> (config not present? already running?)");
                                }
                            }
                        }
                    }
                }
            }

            else {
                $output->writeln("No scripts found. Make sure you add your own scripts to the app/console folder.");
            }
        }

        $timing->stop();

        // need to be called manually?
        $output->writeln($timing->__toString());
    }
}
