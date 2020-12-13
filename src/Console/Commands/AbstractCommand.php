<?php

namespace Antriver\LaravelSiteUtils\Console\Commands;

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

abstract class AbstractCommand extends Command
{
    /**
     * @var null
     */
    protected $isDryRun = null;

    /**
     * If the command is configured to allow a test/dry run.
     * If true, will be a dry run by default unless the --wet option is used.
     *
     * @var bool
     */
    protected $supportsDryRun = false;

    public function __construct()
    {
        parent::__construct();

        $this->addOption(
            'wet',
            '-w',
            InputOption::VALUE_NONE,
            'Commands are dry run by default. Specify wet to perform the action.'
        );
    }

    /**
     * Initializes the command just after the input has been validated.
     *
     * This is mainly useful when a lot of commands extends one main command
     * where some things need to be initialized based on the input arguments and options.
     *
     * @param InputInterface $input An InputInterface instance
     * @param OutputInterface $output An OutputInterface instance
     */
    protected function initialize(InputInterface $input, OutputInterface $output)
    {
        $this->output->writeln("\n*****\n".date('Y-m-d H:i:s')."\n".static::class."\n*****");

        if ($this->supportsDryRun) {
            $this->isDryRun = !$input->getOption('wet');

            if ($this->isDryRun) {
                $this->info("--wet not specified. Performing a dry-run.");
            } else {
                $this->warn("Running for real.");
            }
        }
    }

    /**
     * @param $max
     *
     * @return \Symfony\Component\Console\Helper\ProgressBar
     */
    protected function createProgressBar($max = 0)
    {
        $progress = $this->output->createProgressBar($max);
        $progress->setMessage('');
        $progress->setFormat('%current%/%max% [%bar%] %percent:3s%% %remaining:6s% remaining %memory:6s% %message%');

        return $progress;
    }
}
