<?php

namespace Antriver\LaravelSiteScaffolding\Console\Commands\Scaffolding;

use Antriver\LaravelSiteScaffolding\Console\Commands\AbstractCommand;

class CleanDefaultFiles extends AbstractCommand
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'scaffolding:clean-default-files';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Remove some unused files in a default Laravel install.';

    protected $files = [
        'tests/TestCase.php',
        'tests/Feature/ExampleTest.php',
        'tests/Unit/ExampleTest.php',
    ];

    protected $supportsDryRun = true;

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $basePath = app()->basePath().'/';

        foreach ($this->files as $file) {
            $filePath = $basePath.$file;
            if (file_exists($filePath)) {
                if (is_dir($filePath)) {
                    $this->info("Removing directory ${filePath}");
                    if (!$this->isDryRun) {
                        rmdir($filePath);
                    }
                } else {
                    $this->info("Removing directory ${filePath}");
                    if (!$this->isDryRun) {
                        unlink($filePath);
                    }
                }
            } else {
                $this->output->writeln("Didn't find ${filePath}");
            }
        }
    }
}
