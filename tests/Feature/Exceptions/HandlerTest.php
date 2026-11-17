<?php

namespace Antriver\LaravelSiteUtilsTests\Feature\Exceptions;

use Antriver\LaravelSiteUtilsTests\Feature\AbstractFeatureTest;
use Illuminate\Contracts\Debug\ExceptionHandler;
use Intervention\Image\Exceptions\ImageDecoderException;
use Intervention\Image\Exceptions\InvalidArgumentException;

class HandlerTest extends AbstractFeatureTest
{
    public function testDoesNotReportUndecodableImage()
    {
        $this->assertFalse(app(ExceptionHandler::class)->shouldReport(new ImageDecoderException()));
    }

    public function testReportsImageArgumentErrors()
    {
        $this->assertTrue(app(ExceptionHandler::class)->shouldReport(new InvalidArgumentException()));
    }
}
