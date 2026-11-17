<?php

namespace Antriver\LaravelSiteUtilsTests\Feature\Images;

use Antriver\LaravelSiteUtils\Images\AbstractImageProcessor;
use Antriver\LaravelSiteUtils\Images\ImageFileRepository;
use Antriver\LaravelSiteUtilsTests\Feature\CreatesApplication;
use Illuminate\Foundation\Testing\TestCase;
use Intervention\Image\Interfaces\ImageInterface;
use PHPUnit\Framework\Attributes\RequiresPhpExtension;
use Symfony\Component\HttpFoundation\File\File;

#[RequiresPhpExtension('imagick')]
class AbstractImageProcessorTest extends TestCase
{
    use CreatesApplication;
    use CreatesTestImagesTrait;

    public function testToThumbnailCropsToSize()
    {
        $this->setUpImageStorage();
        $image = (new ImageFileRepository())->makeImageFromFile(new File($this->createPng(20, 10)))->getImage();

        $processor = new class extends AbstractImageProcessor {
            public function process(ImageInterface $image)
            {
                $this->toThumbnail($image, 5, 5);

                return $image;
            }
        };
        $processor->process($image);

        $this->assertSame(5, $image->width());
        $this->assertSame(5, $image->height());
    }
}
