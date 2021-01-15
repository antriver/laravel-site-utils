<?php

namespace Antriver\LaravelSiteUtils\Lang;

//use Antriver\LaravelSiteUtils\Images\Traits\MatchesImageMarkup;
use Antriver\LaravelSiteUtils\Images\Image;

/**
 * Formats text with some very limited markdown support.
 */
class TextPresenter
{
    //use MatchesImageMarkup;

    private $parsedown;

    public function __construct(Parsedown $parsedown)
    {
        $this->parsedown = $parsedown;
    }

    /**
     * Format a single line of text. (For post titles, notifications).
     * Allows basic bold and underline etc. but no links, images, or block elements.
     *
     * @param $text
     *
     * @return string
     */
    public function formatLine(string $text): string
    {
        // Escape everything first
        $text = e($text, false);

        $this->parsedown->setUrlsLinked(false);
        $text = $this->parsedown->lineWithBreaks($text);
        $this->parsedown->setUrlsLinked(true);

        $text = $this->parseSmilies($text);
        $text = $this->stripHeadings($text);

        return $text;
    }

    /**
     * Format multiple paragraphs of text (For comments, messages, post bodies).
     * Allows URLs, lists, images, and paragraphs.
     *
     * @param string $text
     * @param bool $urlsLinked
     * @param bool $fullsizeImages
     *
     * @return string
     */
    public function format(string $text, bool $urlsLinked = true, bool $fullsizeImages = false): string
    {
        // Escape everything first
        $text = e($text);

        // Undo the escaping of > to make markdown quotes work.
        $text = preg_replace_callback(
            '/^(&gt;)+/um',
            function ($match) {
                $count = mb_substr_count($match[0], '&gt;');

                return str_repeat('>', $count);
            },
            $text
        );

        if (!$urlsLinked) {
            $this->parsedown->setUrlsLinked(false);
        }
        $text = $this->parsedown->text($text);
        $this->parsedown->setUrlsLinked(true);

        $text = $this->parseSmilies($text);
        // $text = $this->parseUploadedImages($text, $fullsizeImages);

        $text = preg_replace(
            '/(::: diff)(.*?)(:::)/is',
            '<div class="diff">$2</div>',
            $text
        );

        return $text;
    }

    public function strip($text)
    {
        return strip_tags(nl2br($this->format($text)));
    }

    /**
     * Convert [IMAGE:123] to embedded image.
     * This isn't done with markdown because we don't want users to be able to use image markdown.
     *
     * @param string $text
     * @param bool $fullsizeImages
     *
     * @return string
     */
    private function parseUploadedImages(string $text, bool $fullsizeImages): string
    {
        $spacerUrl = config('app.spacer_url');

        $images = $this->matchImageMarkupInText($text);

        foreach ($images[0] as $i => $string) {
            $id = $images[1][$i];
            $viewUrl = Image::viewUrl($id);
            $thumbUrl = image_thumb_fit($viewUrl, ...ImageSize::CONTENT_IMAGE);

            if ($fullsizeImages) {
                $replacement = '<a href="'.$viewUrl.'" target="_blank" class="upl-img upl-img-full lightbox" rel="noopener">'
                    .'<img src="'.$viewUrl.'" alt="Image in content" />'
                    .'</a>';
            } else {
                $replacement = '<a href="'.$viewUrl.'" target="_blank" class="upl-img" data-lightbox="'.$viewUrl.'" rel="noopener">'
                    .'<img src="'.$spacerUrl.'" class="lazyload" data-src="'.$thumbUrl.'" alt="Image in content"/>'
                    .'</a>';
            }

            $text = str_replace($string, $replacement, $text);
        }

        return $text;
    }

    private function parseSmilies(string $text): string
    {
        $smilies = config('smilies.smilies');
        $spacerUrl = config('app.spacer_url');
        $url = config('app.smilies_url');

        foreach ($smilies as $name) {
            $text = str_ireplace(
                '('.$name.')',
                '<span class="smilie">'
                .'<img data-src="'.$url.$name.'.gif" src="'.$spacerUrl.'" title="('.$name.')" alt="'.$name.' smilie" class="lazyload" />'
                .'</span>',
                $text
            );
        }

        return $text;
    }

    private function stripHeadings(string $text): string
    {
        return preg_replace(
            '/^(\s*#+\s*)/m',
            '',
            $text
        );
    }

    public static function stripNonWords(string $string): string
    {
        return preg_replace("/[^A-Za-z0-9 ]/", "", $string);
    }

    public static function formatScore(int $score)
    {
        if ($score > 0) {
            $score = '<span class="green">+'.number_format($score).'</span>';
        } elseif ($score < 0) {
            $score = '<span class="red">'.number_format($score).'</span>';
        }

        return $score;
    }
}
