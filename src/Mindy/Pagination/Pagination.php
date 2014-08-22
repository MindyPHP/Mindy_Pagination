<?php

namespace Mindy\Pagination;

use Mindy\Utils\RenderTrait;

/**
 * All rights reserved.
 *
 * @author Falaleev Maxim
 * @email max@studio107.ru
 * @version 1.0
 * @company Studio107
 * @site http://studio107.ru
 * @date 09/05/14.05.2014 14:56
 */
class Pagination extends BasePagination
{
    use RenderTrait;

    public function toJson()
    {
        return [
            'objects' => $this->data,
            'meta' => [
                'total' => $this->total,
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
