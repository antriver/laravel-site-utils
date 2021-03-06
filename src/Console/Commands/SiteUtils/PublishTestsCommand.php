<?php

namespace Antriver\LaravelSiteUtils\Console\Commands\SiteUtils;

use Antriver\LaravelSiteUtils\Console\Commands\AbstractCommand;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use SplFileInfo;
use Symfony\Component\Finder\Finder;

class PublishTestsCommand extends AbstractCommand
{
    use MakesClassesTrait;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'site-utils:publish-tests  {--trait-dir=} {--trait-namespace=} {--output-dir=} {--output-namespace=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create concrete tests from the site-utils test traits.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        if (!($traitsDirectory = $this->option('trait-dir'))) {
            $traitsDirectory = realpath(__DIR__.'/../../../Testing/RouteTests');
        }
        $this->output->writeln("Input directory: {$traitsDirectory}");

        if (!($traitsNamespace = $this->option('trait-namespace'))) {
            $traitsNamespace = 'Antriver\LaravelSiteUtils\Testing\RouteTests';
        }
        $this->output->writeln("Input namespace: {$traitsNamespace}");

        if (!($outputDirectory = $this->option('output-dir'))) {
            $outputDirectory = app()->basePath().'/tests/Feature/Api';
        }
        $this->output->writeln("Output directory: {$outputDirectory}");

        if (!is_dir($outputDirectory)) {
            mkdir($outputDirectory, 0777, true);
        }

        if (!($outputNamespace = $this->option('output-namespace'))) {
            $outputNamespace = trim(app()->getNamespace(), '\\').'Tests\\Feature\\Api';

            $this->makeAbstractTestCase();
            $this->makeAbstractApiTestCase();
        }
        $this->output->writeln("Output namespace: {$outputNamespace}");

        $files = $this->getFiles($traitsDirectory);

        $siteUtilsOutputDirectory = $outputDirectory.'/SiteUtils';
        if (!is_dir($siteUtilsOutputDirectory)) {
            mkdir($siteUtilsOutputDirectory, 0777, true);
        }

        foreach ($files as $file) {
            $traitClass = $this->getClassNameFromFile(
                $file,
                $traitsDirectory,
                $traitsNamespace
            );

            $shortTraitName = $this->getShortClassName($traitClass);
            $testName = $this->getTestName($shortTraitName);

            $fileContents = $this->buildFileContents($traitClass, $testName, $outputNamespace);

            $testPath = $siteUtilsOutputDirectory.'/'.$testName.'.php';
            file_put_contents($testPath, $fileContents);

            $this->info("Wrote {$testPath}");
        }
    }

    protected function makeAbstractTestCase()
    {
        $outputNamespace = $this->getAppNamespace().'Tests';
        $outputDirectory = $this->getAppPath().'/tests';
        $path = $outputDirectory.'/AbstractTestCase.php';

        if (!file_exists($path)) {
            $contents = <<<EOL
<?php

namespace {$outputNamespace};

use Antriver\LaravelSiteUtils\Testing\Traits\TestCaseTrait;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Foundation\Testing\TestCase;

abstract class AbstractTestCase extends TestCase
{
    use CreatesApplication;
    use DatabaseTransactions;
    use TestCaseTrait;
}

EOL;
            file_put_contents($path, $contents);
            $this->info("Wrote {$path}");
        } else {
            $this->output->writeln("{$path} already exists");
        }
    }

    protected function makeAbstractApiTestCase()
    {
        $outputNamespace = trim(app()->getNamespace(), '\\').'Tests\\Feature\\Api';
        $outputDirectory = app()->basePath().'/tests/Feature/Api';
        $path = $outputDirectory.'/AbstractApiTestCase.php';

        $testCaseNamespace = trim(app()->getNamespace(), '\\').'Tests';

        if (!file_exists($path)) {
            $contents = <<<EOL
<?php

namespace {$outputNamespace};

use Antriver\LaravelSiteUtils\Testing\Traits\ApiTestCaseTrait;
use {$testCaseNamespace}\AbstractTestCase;

abstract class AbstractApiTestCase extends AbstractTestCase
{
    use ApiTestCaseTrait;
}

EOL;
            file_put_contents($path, $contents);
            $this->info("Wrote {$path}");
        } else {
            $this->output->writeln("{$path} already exists");
        }
    }

    /**
     * @param string $path
     *
     * @return SplFileInfo[]|iterable
     */
    protected function getFiles(string $path)
    {
        /** @var SplFileInfo[]|iterable $files */
        return $files = (new Finder)->files()->in($path);
    }

    /**
     * Extract the class name from the given file path.
     *
     * @param \SplFileInfo $file
     * @param string $basePath
     * @param string $baseNamespace
     *
     * @return string
     */
    protected function getClassNameFromFile(SplFileInfo $file, string $basePath, string $baseNamespace): string
    {
        // Remove the basePath from the file's absolute path to get its path relative to the app root.
        $relativePath = trim(Str::replaceFirst($basePath, '', $file->getRealPath()), DIRECTORY_SEPARATOR);

        // Replace directory separators with namespace separators.
        $className = str_replace(DIRECTORY_SEPARATOR, '\\', $relativePath);

        // Class name relative to root namespace.
        $className = ucfirst(Str::replaceLast('.php', '', $className));

        // Add the root namespace.
        $className = $baseNamespace.'\\'.$className;

        return $className;
    }

    protected function getShortClassName(string $className): string
    {
        return Arr::last(explode('\\', $className));
    }

    protected function getTestName(string $traitClassName): string
    {
        return preg_replace('/Trait$/', '', $traitClassName);
    }

    protected function buildFileContents(string $traitClass, string $testName, string $outputNamespace)
    {
        $shortTraitClass = $this->getShortClassName($traitClass);;

        return <<<EOL
<?php

namespace {$outputNamespace}\\SiteUtils;

use {$outputNamespace}\\AbstractApiTestCase;
use {$traitClass};

class {$testName} extends AbstractApiTestCase
{
    use {$shortTraitClass};
}

EOL;
    }
}
