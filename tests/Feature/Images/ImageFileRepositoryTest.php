<?php

namespace Antriver\LaravelSiteUtilsTests\Feature\Images;

use Antriver\LaravelSiteUtils\Images\ImageFileRepository;
use Antriver\LaravelSiteUtilsTests\Feature\CreatesApplication;
use Illuminate\Foundation\Testing\TestCase;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Exceptions\DecoderException;
use PHPUnit\Framework\Attributes\RequiresPhpExtension;
use Symfony\Component\HttpFoundation\File\File;

#[RequiresPhpExtension('imagick')]
class ImageFileRepositoryTest extends TestCase
{
    use CreatesApplication;
    use CreatesTestImagesTrait;

    private string $cloudDisk;

    protected function setUp(): void
    {
        parent::setUp();

        $this->cloudDisk = $this->setUpImageStorage();
    }

    public function testMakeImageFromFileReadsDimensions()
    {
        $imageOnDisk = (new ImageFileRepository())->makeImageFromFile(new File($this->createPng(20, 10)));

        $this->assertSame(20, $imageOnDisk->getImage()->width());
        $this->assertSame(10, $imageOnDisk->getImage()->height());
    }

    public function testMakeImageFromFileRejectsNonImage()
    {
        $path = tempnam(sys_get_temp_dir(), 'lsu').'.txt';
        file_put_contents($path, 'not an image');

        $this->expectException(DecoderException::class);

        (new ImageFileRepository())->makeImageFromFile(new File($path));
    }

    public function testMakeImageFromStringReadsBinary()
    {
        $imageOnDisk = (new ImageFileRepository())->makeImageFromString(file_get_contents($this->createPng(20, 10)));

        $this->assertSame(20, $imageOnDisk->getImage()->width());
    }

    public function testMakeImageFromStringRejectsFilePath()
    {
        $this->expectException(DecoderException::class);

        (new ImageFileRepository())->makeImageFromString($this->createPng(20, 10));
    }

    public function testPersistImageStoresFileAndModel()
    {
        $repository = new ImageFileRepository();
        $imageOnDisk = $repository->makeImageFromFile(new File($this->createPng(20, 10)));

        $model = $repository->persistImage($imageOnDisk->getImage(), 'avatars');

        $this->assertSame(20, $model->width);
        $this->assertSame(10, $model->height);
        $this->assertSame('avatars', $model->directory);
        Storage::disk($this->cloudDisk)->assertExists('avatars/'.$model->filename);
    }
}
