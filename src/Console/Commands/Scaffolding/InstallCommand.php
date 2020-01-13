<?php

namespace Antriver\LaravelSiteScaffolding\Console\Commands\Scaffolding;

use Antriver\LaravelSiteScaffolding\Console\Commands\AbstractCommand;

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

    protected $traitsForApiControllers = [
        \Antriver\LaravelSiteScaffolding\Auth\Http\AuthControllerTrait::class => 'AuthController',
        \Antriver\LaravelSiteScaffolding\EmailVerification\Http\EmailVerificationControllerTrait::class => 'EmailVerificationController',
        \Antriver\LaravelSiteScaffolding\Auth\Http\ForgotPasswordControllerTrait::class => 'ForgotPasswordController',
        \Antriver\LaravelSiteScaffolding\Auth\Http\RegisterControllerTrait::class => 'RegisterController',
        \Antriver\LaravelSiteScaffolding\Auth\Http\PasswordResetControllerTrait::class => 'PasswordResetController',
        \Antriver\LaravelSiteScaffolding\Mail\Http\SnsControllerTrait::class => 'SnsController',
    ];

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->makeAbstractController();
        $this->makeAbstractApiController();
        $this->makeApiControllersForTraits();
    }

    protected function makeAbstractController()
    {
        $namespace = $this->getAppNamespace().'\Http\Controllers';
        $path = app()->basePath().'/app/Http/Controllers/AbstractController.php';
        $contents = <<<EOL
<?php

namespace {$namespace};

use Antriver\LaravelSiteScaffolding\Http\Controllers\ControllerTrait;
use Illuminate\Routing\Controller;

abstract class AbstractController extends Controller 
{
    use ControllerTrait;
}

EOL;
        $this->writeFileIfNotExists($path, $contents);
    }

    protected function makeAbstractApiController()
    {
        $namespace = $this->getAppNamespace().'\Http\Controllers\Api';
        $path = app()->basePath().'/app/Http/Controllers/Api/AbstractApiController.php';
        $contents = <<<EOL
<?php

namespace {$namespace};

use Antriver\LaravelSiteScaffolding\Http\Controllers\ApiControllerTrait;
use {$this->getAppNamespace()}\Http\Controllers\AbstractController;

abstract class AbstractApiController extends AbstractController
{
    use ApiControllerTrait;
}

EOL;
        $this->writeFileIfNotExists($path, $contents);
    }

    protected function makeApiControllersForTraits()
    {
        foreach ($this->traitsForApiControllers as $traitClass => $controllerName) {
            $this->makeApiControllerForTrait($traitClass, $controllerName);
        }
    }

    protected function makeApiControllerForTrait(string $traitClass, string $controllerName)
    {
        $traitName = explode('\\', $traitClass);
        $traitName = array_pop($traitName);

        $namespace = $this->getAppNamespace().'\Http\Controllers\Api';
        $path = app()->basePath().'/app/Http/Controllers/Api/'.$controllerName.'.php';
        $contents = <<<EOL
<?php

namespace {$namespace};

use {$traitClass};

class {$controllerName} extends AbstractApiController
{
    use {$traitName};
}

EOL;
        $this->writeFileIfNotExists($path, $contents);
    }
}
