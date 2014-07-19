<?php

use Mindy\Pagination\Pagination;
use Mindy\Query\Query;

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

    public function testPaginateQuery()
    {
        $connection = new \Mindy\Query\Connection([
            'dsn' => 'sqlite::memory:'
        ]);
        $cmd = $connection->createCommand();
        $cmd->createTable('test', ['id' => 'auto'])->execute();
        $cmd->insert('test', ['id' => 1])->execute();
        $cmd->insert('test', ['id' => 2])->execute();
        $cmd->insert('test', ['id' => 3])->execute();
        $cmd->insert('test', ['id' => 4])->execute();

        $query = new Query();
        $query->db = $connection;

        $query->select(['id'])->from('test');
        $pager = new Pagination($query, [
            'pageSize' => 1
        ]);
        $this->assertEquals([['id' => 1]], $pager->paginate());
    }
}
