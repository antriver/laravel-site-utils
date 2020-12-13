<?php

namespace Antriver\LaravelSiteUtils\Pagination;

use Illuminate\Support\HtmlString;

class LengthAwarePaginator extends \Illuminate\Pagination\LengthAwarePaginator
{
    /**
     * Get a URL for a given page number.
     * Replaces ':page' in the path with the correct page number. For pretty URLs.
     *
     * @param int $page
     *
     * @return string
     */
    public function url($page)
    {
        if ($page <= 0) {
            $page = 1;
        }

        if (strpos($this->path, ':page') === false) {
            // If the path has no :page placeholder, use the original url generator so it the page
            // goes into the query.
            return parent::url($page);
        }

        return str_replace(':page', $page, $this->path);
    }

    /**
     * Public version of the protected appendArray method.
     *
     * Add an array of query string values.
     *
     * @param array $keys
     *
     * @return \Illuminate\Pagination\LengthAwarePaginator
     */
    public function appendsArray(array $keys)
    {
        return parent::appendArray($keys);
    }

    /**
     * Render the paginator using the given view.
     * Overridden so the custom UrlWindow is used.
     *
     * @param string $view
     * @param array $data
     *
     * @return string
     */
    public function render($view = null, $data = [])
    {
        $elements = $this->getElements();

        return new HtmlString(
            static::viewFactory()
                ->make(
                    $view ?: static::$defaultView,
                    [
                        'paginator' => $this,
                        'elements' => array_filter($elements),
                    ]
                )
                ->render()
        );
    }

    public function getElements(): array
    {
        $window = $this->getUrlWindow();

        return array_filter(
            [
                $window['first'],
                is_array($window['slider']) ? '...' : null,
                $window['slider'],
                is_array($window['last']) ? '...' : null,
                $window['last'],
            ]
        );
    }

    public function getFirstItemOnPage()
    {
        return ($this->currentPage() - 1) * $this->perPage() + 1;
    }

    public function getLastItemOnPage()
    {
        return min($this->total(), $this->currentPage() * $this->perPage());
    }

    /**
     * @return array
     */
    protected function getUrlWindow()
    {
        // Note that UrlWindow when used here is our custom one (because of the namespace)
        return UrlWindow::make($this);
    }

    /**
     * Get the instance as an array.
     *
     * @return array
     */
    public function toArray()
    {
        return [
            'currentPage' => $this->currentPage(),
            'items' => $this->items->toArray(),
            'firstPageUrl' => $this->url(1),
            'from' => $this->firstItem(),
            'lastPage' => $this->lastPage(),
            'lastPageUrl' => $this->url($this->lastPage()),
            'nextPageUrl' => $this->nextPageUrl(),
            'path' => $this->path,
            'perPage' => $this->perPage(),
            'prevPageUrl' => $this->previousPageUrl(),
            'to' => $this->lastItem(),
            'total' => $this->total(),
        ];
    }
}
