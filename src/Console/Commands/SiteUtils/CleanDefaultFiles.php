<?php

namespace Antriver\LaravelSiteUtils\Console\Commands\SiteUtils;

use Antriver\LaravelSiteUtils\Console\Commands\AbstractCommand;

class CleanDefaultFiles extends AbstractCommand
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'site-utils:clean-default-files';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Remove some unused files in a default Laravel install.';

    protected $files = [
        '.styleci.yml',
        'app/Http/Controllers/Auth',
        'app/Http/Controllers/Controller.php',
        'app/User.php',
        'database/migrations/2014_10_12_000000_create_users_table.php',
        'database/migrations/2014_10_12_100000_create_password_resets_table.php',
        'public/css',
        'public/js',
        'public/web.config',
        'resources/js',
        'resources/sass',
        'server.php',
        'tests/Feature/ExampleTest.php',
        'tests/TestCase.php',
        'tests/Unit/ExampleTest.php',
        'webpack.mix.js',
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
                    $this->info("Removing file ${filePath}");
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
