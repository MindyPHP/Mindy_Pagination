<?php

namespace Mindy\Pagination;

use Mindy\Helper\Traits\RenderTrait;

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
                'total' => (int)$this->getTotal(),
                'pages_count' => $this->getPagesCount(),
                'page' => $this->getPage(),
                'page_size' => $this->getPageSize(),
            ]
        ];
    }

    public function render($view = "core/pager/pager.html")
    {
        return $this->renderTemplate($view, ['this' => $this]);
    }
}
