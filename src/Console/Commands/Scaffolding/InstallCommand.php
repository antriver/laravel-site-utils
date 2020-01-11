<?php

namespace Antriver\LaravelSiteScaffolding\Console\Commands\Scaffolding;

use Antriver\LaravelSiteScaffolding\Console\Commands\AbstractCommand;
use Antriver\LaravelSiteScaffolding\Http\Controllers\ControllerTrait;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use SplFileInfo;
use Symfony\Component\Finder\Finder;

class InstallCommand extends AbstractCommand
{
    use MakesClassesTrait;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'scaffolding:install';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create default files.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->makeAbstractController();
    }

    protected function makeAbstractController()
    {
        $namespace = $this->getAppNamespace().'\\Http\\Controllers';
        $path = app()->basePath().'/app/Http/Controllers/AbstractController.php';
        $contents = <<<EOL
<?php

namespace {$namespace};

use Antriver\LaravelSiteScaffolding\Http\Controllers\ControllerTrait;
use Illuminate\Routing\Controller

abstract class AbstractController extends Controller 
{
    use ControllerTrait;
}

EOL;
        $this->writeFileIfNotExists($path, $contents);
    }
}
