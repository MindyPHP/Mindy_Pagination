<?php

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
class PaginationTest extends TestCase
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
        $pager = new Pagination([
            'source' => $data,
            'pageSize' => $pageSize
        ]);
        $pager->setPage($page);
        $this->assertEquals($result, $pager->paginate()->data);
    }

    public function testPagerInit()
    {
        $pager = new Pagination([
            'source' => [1, 2, 3, 4, 5, 6, 7, 8, 9, 10],
            'pageSize' => 2
        ]);
        $pager->paginate();
        $this->assertEquals(5, $pager->getPagesCount());
        $this->assertEquals(10, $pager->getTotal());
        $this->assertTrue($pager->hasNextPage());
        $this->assertTrue($pager->hasPrevPage());
        $this->assertEquals(1, $pager->getCurrentPage());

        $this->assertEquals("1 1 10 1\n", $pager->render());
    }
}
