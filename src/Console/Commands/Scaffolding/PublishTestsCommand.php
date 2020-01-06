<?php

namespace Antriver\LaravelSiteScaffolding\Console\Commands\Scaffolding;

use Antriver\LaravelSiteScaffolding\Console\Commands\AbstractCommand;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use SplFileInfo;
use Symfony\Component\Finder\Finder;

class PublishTestsCommand extends AbstractCommand
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'scaffolding:publish-tests  {--trait-dir=} {--trait-namespace=} {--output-dir=} {--output-namespace=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create concrete tests from the scaffolding test traits.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        if (!($traitsDirectory = $this->option('trait-dir'))) {
            $traitsDirectory = realpath(__DIR__.'/../../../Testing/RouteTests');
        }
        $this->info("Input directory: {$traitsDirectory}");

        if (!($traitsNamespace = $this->option('trait-namespace'))) {
            $traitsNamespace = 'Antriver\LaravelSiteScaffolding\Testing\RouteTests';
        }
        $this->info("Input namespace: {$traitsNamespace}");

        if (!($outputDirectory = $this->option('output-dir'))) {
            $outputDirectory = app()->basePath().'/tests/Feature/Api';
        }
        $this->info("Output directory: {$outputDirectory}");

        if (!($outputNamespace = $this->option('output-namespace'))) {
            $outputNamespace = trim(app()->getNamespace(), '\\').'Tests\\Feature\\Api';
        }
        $this->info("Output namespace: {$outputNamespace}");

        $files = $this->getFiles($traitsDirectory);

        $outputDirectory = $outputDirectory.'/Scaffolding';
        if (!is_dir($outputDirectory)) {
            mkdir($outputDirectory);
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

            $testPath = $outputDirectory.'/'.$testName.'.php';
            file_put_contents($testPath, $fileContents);

            $this->info("Wrote {$testPath}");
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

namespace {$outputNamespace}\\Scaffolding;

use {$outputNamespace}\\AbstractApiTestCase;
use {$traitClass};

class {$testName} extends AbstractApiTestCase
{
    use {$shortTraitClass};
}

EOL;
    }
}
