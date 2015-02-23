<?php

namespace Mindy\Pagination;

use Mindy\Utils\RenderTrait;

/**
 * Class Pagination
 * @package Mindy\Pagination
 */
class Pagination extends BasePagination
{
    use RenderTrait;

    public function __toString()
    {
        return (string)$this->render();
    }

    public function toJson()
    {
        return [
            'objects' => $this->data,
            'meta' => [
                'pagesCount' => $this->getPagesCount(),
                'page' => $this->page,
                'pageSize' => $this->pageSize,
            ]
        ];
    }

    public function render($view = "core/pager/pager.html")
    {
        return $this->renderTemplate($view, ['this' => $this]);
    }
}
