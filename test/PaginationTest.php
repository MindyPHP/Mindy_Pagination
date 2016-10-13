<?php

namespace Mindy\Pagination\Tests;

use Mindy\Pagination\Pagination;

/**
 * All rights reserved.
 *
 * @author Falaleev Maxim
 * @email max@studio107.ru
 * @version 1.0
 * @company Studio107
 * @site http://studio107.ru
 * @date 17/04/14.04.2014 16:45
 */
class PaginationTest extends \PHPUnit_Framework_TestCase
{
    public function provider()
    {
        return [
            [[1, 2, 3], 1, 1, [1]],
            [[1, 2, 3], 1, 3, [1, 2, 3]],
            [[1, 2, 3], 2, 1, [2]],
            [[1, 2, 3, 4], 2, 2, [3, 4]],
            [[1, 2, 3], 3, 1, [3]],
        ];
    }

    /**
     * @dataProvider provider
     */
    public function testPager($data, $page, $pageSize, $result)
    {
        $pager = new Pagination($data, [
            'pageSize' => $pageSize
        ]);
        $pager->setPage($page);
        $this->assertEquals($result, $pager->paginate());
    }

    public function testPagerInit()
    {
        $pager = new Pagination([1, 2, 3, 4, 5, 6, 7, 8, 9, 10], [
            'pageSize' => 2
        ]);
        $pager->paginate();
        $this->assertEquals(5, $pager->getPagesCount());
        $this->assertEquals(10, $pager->getTotal());
        $this->assertTrue($pager->hasNextPage());
        $this->assertFalse($pager->hasPrevPage());
        $this->assertEquals(1, $pager->getCurrentPage());
    }

    public function testPaginateJson()
    {
        $pager = new Pagination([1, 2, 3, 4, 5, 6, 7, 8, 9, 10], [
            'pageSize' => 2
        ]);
        $this->assertEquals([1, 2], $pager->paginate());
        $this->assertEquals([
            'objects' => [1, 2],
            'meta' => [
                'page' => 1,
                'pageSize' => 2,
                'total' => 10
            ]
        ], $pager->toJson());
    }
}
