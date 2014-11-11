<?php
/**
 *
 *
 * All rights reserved.
 *
 * @author Falaleev Maxim
 * @email max@studio107.ru
 * @version 1.0
 * @company Studio107
 * @site http://studio107.ru
 * @date 11/11/14.11.2014 16:24
 */

namespace Mindy\Pagination\Interfaces;


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
