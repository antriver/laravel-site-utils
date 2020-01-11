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
        'app/User.php',
        'app/Http/Controllers/Auth',
        'database/migrations/2014_10_12_000000_create_users_table.php',
        'database/migrations/2014_10_12_100000_create_password_resets_table.php',
        'tests/TestCase.php',
        'tests/Feature/ExampleTest.php',
        'tests/Unit/ExampleTest.php',
        'public/css',
        'public/js',
        'public/web.config',
        'resources/js',
        'resources/sass',
        '.styleci.yml',
        'server.php',
        'webpack.mix.js'
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
                        passthru("rm -r ".escapeshellarg($filePath));
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
