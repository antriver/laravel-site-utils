<?php

namespace Antriver\LaravelSiteUtilsTests\Unit\Lang;

use Antriver\LaravelSiteUtilsTests\Unit\AbstractUnitTestCase;
use Antriver\LaravelSiteUtils\Lang\LanguageHelpers;
use PHPUnit\Framework\Attributes\DataProvider;

class LanguageHelpersTest extends AbstractUnitTestCase
{
    public static function dataForTestWordTruncate(): array
    {
        return [
            [
                'hello',
                'hello',
            ],
            [
                'hello world',
                'hello...',
            ],
            [
                'abcdefghijklmnopqrstuvwxyz',
                'abcdefg...',
            ],
            [
                'abcdefghij',
                'abcdefghij',
            ],
            [
                'abcdefghijk',
                'abcdefg...',
            ],
            [
                'abcdefghij ',
                'abcdefg...',
            ],
            [
                'abcdefghi j',
                'abcdefg...',
            ],
            [
                'abcdefgh ij',
                'abcdefg...',
            ],
            [
                'abcdefg hij',
                'abcdefg...',
            ],
            [
                'abcdef ghij',
                'abcdef...',
            ],
            [
                'a bcdefghij',
                'a...',
            ],
        ];
    }

    #[DataProvider('dataForTestWordTruncate')]
    public function testWordTruncate($input, $expect)
    {
        $result = LanguageHelpers::wordTruncate($input, 10);
        $this->assertSame($expect, $result);
    }

    public static function dataForTestWordTruncateDetail(): array
    {
        return [
            [
                'hello',
                [
                    'string' => 'hello',
                    'breakpoint' => null,
                    'remainder' => null,
                ],
            ],
            [
                'hello world',
                [
                    'string' => 'hello...',
                    'breakpoint' => 5,
                    'remainder' => 'world',
                ],
            ],
            [
                'abcdefghijklmnopqrstuvwxyz',
                [
                    'string' => 'abcdefg...',
                    'breakpoint' => 7,
                    'remainder' => 'hijklmnopqrstuvwxyz',
                ],
            ],
            [
                'abcdefghij',
                [
                    'string' => 'abcdefghij',
                    'breakpoint' => null,
                    'remainder' => null,
                ],
            ],
            [
                'abcdefghijk',
                [
                    'string' => 'abcdefg...',
                    'breakpoint' => 7,
                    'remainder' => 'hijk',
                ],
            ],
            [
                'abcdefghij ',
                [
                    'string' => 'abcdefg...',
                    'breakpoint' => 7,
                    'remainder' => 'hij',
                ],
            ],
            [
                'abcdefghi j',
                [
                    'string' => 'abcdefg...',
                    'breakpoint' => 7,
                    'remainder' => 'hi j',
                ],
            ],
            [
                'abcdefgh ij',
                [
                    'string' => 'abcdefg...',
                    'breakpoint' => 7,
                    'remainder' => 'h ij',
                ],
            ],
            [
                'abcdefg hij',
                [
                    'string' => 'abcdefg...',
                    'breakpoint' => 7,
                    'remainder' => 'hij',
                ],
            ],
            [
                'abcdef ghij',
                [
                    'string' => 'abcdef...',
                    'breakpoint' => 7,
                    'remainder' => 'ghij',
                ],
            ],
            [
                'a bcdefghij',
                [
                    'string' => 'a...',
                    'breakpoint' => 1,
                    'remainder' => 'bcdefghij',
                ],
            ],
        ];
    }

    #[DataProvider('dataForTestWordTruncateDetail')]
    public function testWordTruncateAsSArray($input, $expect)
    {
        $result = LanguageHelpers::wordTruncateDetail($input, 10);
        $this->assertSame($expect, $result);
    }
}
