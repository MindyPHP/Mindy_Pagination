<?php

namespace Mindy\Pagination\Interfaces;

/**
 * Interface IPagination
 * @package Mindy\Pagination
 */
interface IPagination
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
