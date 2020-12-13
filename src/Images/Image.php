<?php

namespace Antriver\LaravelSiteUtils\Images;

use Antriver\LaravelSiteUtils\Models\Base\AbstractModel;
use Antriver\LaravelSiteUtils\Models\Interfaces\BelongsToUserInterface;
use Antriver\LaravelSiteUtils\Models\Traits\BelongsToUserTrait;
use Config;

/**
 * Antriver\LaravelSiteUtils\Images\Image
 *
 * @property int $id
 * @property int|null $userId
 * @property string $directory
 * @property string $filename
 * @property int|null $width
 * @property int|null $height
 * @property int|null $size
 * @property int|null $optimizedSize
 * @property bool $hasThumbnail
 * @property string|null $originalUrl
 * @property \Illuminate\Support\Carbon $createdAt
 * @property string|null $updatedAt
 * @method static \Illuminate\Database\Eloquent\Builder|\Antriver\LaravelSiteUtils\Images\Image newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\Antriver\LaravelSiteUtils\Images\Image newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\Antriver\LaravelSiteUtils\Images\Image query()
 * @method static \Illuminate\Database\Eloquent\Builder|\Antriver\LaravelSiteUtils\Images\Image
 *     whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Antriver\LaravelSiteUtils\Images\Image
 *     whereDirectory($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Antriver\LaravelSiteUtils\Images\Image
 *     whereFilename($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Antriver\LaravelSiteUtils\Images\Image
 *     whereHasThumbnail($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Antriver\LaravelSiteUtils\Images\Image
 *     whereHeight($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Antriver\LaravelSiteUtils\Images\Image whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Antriver\LaravelSiteUtils\Images\Image
 *     whereOptimizedSize($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Antriver\LaravelSiteUtils\Images\Image
 *     whereOriginalUrl($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Antriver\LaravelSiteUtils\Images\Image whereSize($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Antriver\LaravelSiteUtils\Images\Image
 *     whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Antriver\LaravelSiteUtils\Images\Image
 *     whereUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Antriver\LaravelSiteUtils\Images\Image
 *     whereWidth($value)
 * @mixin \Eloquent
 */
class Image extends AbstractModel implements BelongsToUserInterface
{
    use BelongsToUserTrait;

    /*
    const DIRECTORY_CONTENT = 'content';
    const DIRECTORY_COVERS = 'covers';
    const DIRECTORY_POSTS = 'posts';
    const DIRECTORY_PROFILES = 'profiles';
    const DIRECTORY_TOPICS = 'topics';*/

    protected $casts = [
        'id' => 'int',
        'userId' => 'int',
        'hasThumbnail' => 'bool',
    ];

    public $dates = [
        'createdAt',
    ];

    protected $visible = [
        'id',
        'width',
        'height',
    ];

    public $timestamps = false;

    public function toArray()
    {
        $array = parent::toArray();

        $array['url'] = $this->getUrl();

        return $array;
    }

    public function getUrl()
    {
        return Config::get('app.upload_url').'/'.$this->getPathname();
    }

    public function getPathname()
    {
        return $this->directory.'/'.$this->filename;
    }

    /**
     * @return string[]
     */
    public static function directories(): array
    {
        return [];
    }
}
