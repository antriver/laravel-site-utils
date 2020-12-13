<?php

namespace Antriver\LaravelSiteUtils\Lang;

use Antriver\LaravelSiteUtils\Images\ImageSize;

class Parsedown extends \Parsedown
{
    protected $BlockTypes = [
        '#' => ['Header'],
        '*' => ['Rule', 'List'],
        //'+' => ['List'],
        '-' => ['SetextHeader', 'Table', 'Rule', 'List'],

        /*'0' => ['List'],
        '1' => ['List'],
        '2' => ['List'],
        '3' => ['List'],
        '4' => ['List'],
        '5' => ['List'],
        '6' => ['List'],
        '7' => ['List'],
        '8' => ['List'],
        '9' => ['List'],*/

        ':' => ['Table'],
        //'<' => array('Comment', 'Markup'),
        //'=' => array('SetextHeader'),
        '>' => ['Quote'],
        //'[' => array('Reference'),
        '_' => ['Rule'],
        //'`' => array('FencedCode'),
        '|' => ['Table'],
        //'~' => array('FencedCode'),
    ];

    protected $InlineTypes = [
        //'"' => array('SpecialCharacter'),
        //'!' => array('Image'),
        //'&' => array('SpecialCharacter'),
        '*' => ['Emphasis'],
        ':' => ['Url'],
        //'<' => array('UrlTag', 'EmailTag', 'Markup', 'SpecialCharacter'),
        //'>' => array('SpecialCharacter'),
        '[' => ['Link'],
        //'_' => array('Emphasis'),
        //'`' => ['Code'],
        '~' => ['Strikethrough'],
        '\\' => ['EscapeSequence'],
        // Custom types
        '_' => ['Underline'],
        '-' => ['DashStrikethrough'],
        '/' => ['SlashEmphasis'],
        '^' => ['Superscript'],
        '+' => ['Ins'],
    ];

    protected $altBlockTypes = [
        '&gt;' => '>',
    ];

    protected $breaksEnabled = true;

    // Add ` to this list if trying to make code work.
    protected $inlineMarkerList = '-*_:~\\/^[+';

    protected $markupEscaped = true;

    protected $specialCharacters = [
        '\\',
        '`',
        '*',
        '_',
        '{',
        '}',
        '[',
        ']',
        '(',
        ')',
        '>',
        '#',
        '+',
        '-',
        '.',
        '!',
        '|',
        '~',
        '-',
        '^',
        '=',
    ];

    protected $unmarkedBlockTypes = [
        //'Code',
    ];

    protected $urlsLinked = true;

    /**
     * Support ins ++like this++
     *
     * @param $Excerpt
     *
     * @return array|null
     */
    protected function inlineIns($Excerpt)
    {
        if (!isset($Excerpt['text'][1])) {
            return null;
        }

        if ($Excerpt['text'][1] === '+' and preg_match('/^\+\+(?=\S)(.+?)(?<=\S)\+\+/', $Excerpt['text'], $matches)) {
            return [
                'extent' => strlen($matches[0]),
                'element' => [
                    'name' => 'ins',
                    'text' => $matches[1],
                    'handler' => 'line',
                ],
            ];
        }

        return null;
    }

    /**
     * Support underline __like this__
     *
     * @param $Excerpt
     *
     * @return array|null
     */
    protected function inlineUnderline($Excerpt)
    {
        if (!isset($Excerpt['text'][1])) {
            return null;
        }

        if ($Excerpt['text'][1] === '_' and preg_match('/^__(?=\S)(.+?)(?<=\S)__/', $Excerpt['text'], $matches)) {
            return [
                'extent' => strlen($matches[0]),
                'element' => [
                    'name' => 'u',
                    'text' => $matches[1],
                    'handler' => 'line',
                ],
            ];
        }

        return null;
    }

    /**
     * Support strikethrough --like this--
     *
     * @param $Excerpt
     *
     * @return array|null
     */
    protected function inlineDashStrikethrough($Excerpt)
    {
        if (!isset($Excerpt['text'][1])) {
            return null;
        }

        if ($Excerpt['text'][1] === '-' and preg_match('/^--(?=\S)(.+?)(?<=\S)--/', $Excerpt['text'], $matches)) {
            return [
                'extent' => strlen($matches[0]),
                'element' => [
                    'name' => 'del',
                    'text' => $matches[1],
                    'handler' => 'line',
                ],
            ];
        }

        return null;
    }

    /**
     * Support emphasis //like this//
     *
     * @param $Excerpt
     *
     * @return array|null
     */
    protected function inlineSlashEmphasis($Excerpt)
    {
        if (!isset($Excerpt['text'][1])) {
            return null;
        }

        if ($Excerpt['text'][1] === '/' and preg_match('$^//(?=\S)(.+?)(?<=\S)//$', $Excerpt['text'], $matches)) {
            return [
                'extent' => strlen($matches[0]),
                'element' => [
                    'name' => 'em',
                    'text' => $matches[1],
                    'handler' => 'line',
                ],
            ];
        }

        return null;
    }

    /**
     * Support superscript like ^this
     *
     * @param $Excerpt
     *
     * @return array|null
     */
    protected function inlineSuperscript($Excerpt)
    {
        if (!isset($Excerpt['text'][1])) {
            return null;
        }
        if (preg_match('/\^(.+?)\b/', $Excerpt['text'], $matches)) {
            return [
                'extent' => strlen($matches[0]),
                'element' => [
                    'name' => 'sup',
                    'text' => $matches[1],
                    'handler' => 'line',
                ],
            ];
        }

        return null;
    }

    protected function inlineUrl($Excerpt)
    {
        if ($this->urlsLinked !== true or !isset($Excerpt['text'][2]) or $Excerpt['text'][2] !== '/') {
            return null;
        }

        if (preg_match('/\bhttps?:[\/]{2}[^\s<]+\b\/*/ui', $Excerpt['context'], $matches, PREG_OFFSET_CAPTURE)) {
            $url = htmlspecialchars($matches[0][0]);
            $text = $this->truncateUrl($url, 50);

            // Detect YouTube URLs.
            $ytMatches = [];
            if (preg_match(
                '/(youtu\.be\/|youtube\.com\/(watch\?(.*&)?v=|(embed|v)\/))([^\?&"\'>]+)/i',
                $url,
                $ytMatches
            )) {
                $ytImageUrl = 'https://img.youtube.com/vi/'.$ytMatches[5].'/0.jpg';
                $ytThumbUrl = image_thumb($ytImageUrl, ...ImageSize::YOUTUBE);

                return [
                    'extent' => strlen($url),
                    'position' => $matches[0][1],
                    'element' => [
                        'name' => 'span',
                        'handler' => 'element',
                        'text' => [
                            'name' => 'img',
                            'attributes' => [
                                'alt' => 'YouTube video thumbnail',
                                'class' => 'lazyload',
                                'src' => config('app.spacer_url'),
                                'data-src' => $ytThumbUrl,
                            ],
                        ],
                        'attributes' => [
                            'class' => 'yt-vid',
                            'data-id' => $ytMatches[5],
                            'href' => $url,
                            'target' => '_blank',
                        ],
                    ]

                    /*'element' => [
                        'name' => 'iframe',
                        'text' => '',
                        'attributes' => [
                            'class' => 'yt-video',
                            'src' => 'https://www.youtube-nocookie.com/embed/'.$ytMatches[5],
                            'frameborder' => 0,
                            'allowfullscreen' => 'yes',
                            'allow' => 'autoplay; encrypted-media',
                        ],
                    ],*/
                ];
            }

            // Detect image URLs.
            if ($this->isUrlImage($url)) {
                return [
                    'extent' => strlen($url),
                    'position' => $matches[0][1],
                    'element' => [
                        'name' => 'a',
                        'handler' => 'element',
                        'text' => [
                            'name' => 'img',
                            'attributes' => [
                                'class' => 'lazyload',
                                'alt' => 'Image in content',
                                'src' => config('app.spacer_url'),
                                'data-src' => image_thumb_fit($url, ...ImageSize::CONTENT_IMAGE),
                                //'data-src' => image_thumb($url, ...ImageSize::CONTENT_IMAGE)
                            ],
                        ],
                        'attributes' => [
                            'class' => 'ext-img',
                            'data-lightbox' => image_thumb($url, 'orig', 'orig'),
                            'href' => $url,
                            'target' => '_blank',
                            'rel' => 'nofollow noopener',
                        ],
                    ],
                ];
            }

            return [
                'extent' => strlen($url),
                'position' => $matches[0][1],
                'element' => [
                    'name' => 'a',
                    'text' => $text,
                    'attributes' => [
                        'href' => $url,
                        'target' => '_blank',
                        'rel' => 'nofollow noopener',
                    ],
                ],
            ];
        }

        return null;
    }

    private function isUrlImage($url)
    {
        return preg_match('/\.(jpeg|jpg|gif|bmp|png)/i', $url);
    }

    private function truncateUrl($string, $limit, $cutter = '...')
    {
        $offset1 = ceil(0.65 * $limit) - 2;
        $offset2 = ceil(0.30 * $limit) - 1;

        if ($limit && mb_strlen($string) > $limit) {
            return mb_substr($string, 0, $offset1).$cutter.mb_substr($string, -$offset2);
        } else {
            return $string;
        }
    }

    public function lineWithBreaks($text)
    {
        # standardize line breaks
        $text = str_replace(["\r\n", "\r"], "\n", $text);

        # remove surrounding line breaks
        $text = trim($text, "\n");

        $text = preg_replace('/(?:(?:\r\n|\r|\n)\s*){2}/s', "\n", $text);

        # iterate through lines to identify blocks
        $markup = $this->line($text);

        # trim line breaks
        $markup = trim($markup, "\n");

        return $markup;
    }
}
