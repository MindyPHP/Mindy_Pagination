<?php

namespace Mindy\Pagination;

/**
 * Interface IPagination
 * @package Mindy\Pagination
 */
interface PaginationInterface
{
    /**
     * @param $limit int
     * @return $this
     */
    public function setLimit($limit);

    /**
     * @param $offset int
     * @return $this
     */
    public function setOffset($offset);

    /**
     * @return array
     */
    public function all();

    /**
     * @return int
     */
    public function getTotal();
}
