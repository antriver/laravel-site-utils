<?php

namespace Antriver\LaravelSiteUtils\Console\Commands\SiteUtils;

use Antriver\LaravelSiteUtils\Console\Commands\AbstractCommand;

class InstallCommand extends AbstractCommand
{
    use MakesClassesTrait;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'site-utils:install';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create default files.';

    protected $traitsForApiControllers = [
        \Antriver\LaravelSiteUtils\Auth\Http\AuthControllerTrait::class => 'AuthController',
        \Antriver\LaravelSiteUtils\Auth\Http\ForgotPasswordControllerTrait::class => 'ForgotPasswordController',
        \Antriver\LaravelSiteUtils\Auth\Http\PasswordResetControllerTrait::class => 'PasswordResetController',
        \Antriver\LaravelSiteUtils\Auth\Http\RegisterControllerTrait::class => 'RegisterController',
        \Antriver\LaravelSiteUtils\EmailVerification\Http\EmailVerificationControllerTrait::class => 'EmailVerificationController',
        \Antriver\LaravelSiteUtils\Mail\Http\SnsControllerTrait::class => 'SnsController',
        \Antriver\LaravelSiteUtils\Users\Http\UserControllerTrait::class => 'UserController',
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

use Antriver\LaravelSiteUtils\Http\Controllers\ControllerTrait;
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

use Antriver\LaravelSiteUtils\Http\Controllers\ApiControllerTrait;
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
