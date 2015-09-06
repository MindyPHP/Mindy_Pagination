<?php

namespace Mindy\Pagination;

use Mindy\Exception\Exception;
use Mindy\Helper\Creator;
use Mindy\Helper\Traits\Accessors;
use Mindy\Helper\Traits\Configurator;
use Mindy\Pagination\Interfaces\IPagination;
use Serializable;

/**
 * Class BasePagination
 * @package Mindy\Pagination
 */
abstract class BasePagination implements Serializable
{
    use Accessors, Configurator;

    /**
     * @var int
     */
    public static $defaultPageSize = 10;
    /**
     * @var string
     */
    public $pageKey;
    /**
     * @var string
     */
    public $pageSizeKey;
    /**
     * @var int
     */
    public $pageSize;
    /**
     * @var array
     */
    public $pageSizes = [10, 20, 50, 100];
    /**
     * @var array|IPagination|\Mindy\Orm\QuerySet|\Mindy\Orm\Manager|\Mindy\Query\Query
     */
    public $source = [];
    /**
     * @var array
     */
    public $data = [];
    /**
     * @var int current page
     */
    public $page;
    /**
     * @var int total records or elements in array
     */
    public $total;
    /**
     * @var int autoincrement pagination classes on the page
     */
    private static $_id = 0;
    /**
     * @var int current pagination id
     */
    public $id;
    /**
     * @var string Pager name
     */
    private $_name;
    /**
     * @var bool is QuerySet?
     */
    public $isQs = false;

    public function __construct($source, array $config = [])
    {
        $this->source = $source;
        $this->configure($config);
        $this->init();
    }

    public function init()
    {
        self::$_id++;

        $this->id = self::$_id;
        if (class_exists('\Mindy\Orm\QuerySet')) {
            $this->isQs = $this->source instanceof \Mindy\Orm\QuerySet;
        }
    }

    public function getUrl($page, $endless = false)
    {
        $uri = parse_url($_SERVER['REQUEST_URI']);
        if (!isset($uri['query'])) {
            $uri['query'] = '';
        }
        parse_str($uri['query'], $params);
        $params[$this->getPageKey()] = $page;
        if ($endless) {
            $params['endless'] = $endless;
        }
        return $uri['path'] . "?" . http_build_query($params);
    }

    public function urlPageSize($pageSize)
    {
        $uri = parse_url($_SERVER['REQUEST_URI']);
        if (!isset($uri['query'])) {
            $uri['query'] = '';
        }
        parse_str($uri['query'], $params);
        $params[$this->getPageSizeKey()] = $pageSize;
        return $uri['path'] . "?" . http_build_query($params);
    }

    /**
     * Return PageSize
     * @return int
     */
    public function getPageSize()
    {
        if (isset($_GET[$this->getPageSizeKey()])) {
            $pageSize = (int)$_GET[$this->getPageSizeKey()];
            $this->pageSize = $pageSize ? $pageSize : self::$defaultPageSize;
        } else if ($this->pageSize === null) {
            $this->pageSize = self::$defaultPageSize;
        }

        if (ceil($this->total / $this->pageSize) < $this->page) {
            header("Location: " . $this->getUrl(1));
        }

        return $this->pageSize;
    }

    /**
     * @return string
     */
    public function getPageSizeKey()
    {
        return empty($this->pageSizeKey) ? $this->getPageKey() . '_PageSize' : $this->pageSizeKey;
    }

    public function getTotal()
    {
        return $this->total;
    }

    /**
     * @return integer number of pages
     */
    public function getPagesCount()
    {
        return ceil($this->getTotal() / $this->getPageSize());
    }

    public function hasNextPage()
    {
        return ($this->getPagesCount() - $this->page) > 0;
    }

    public function hasPrevPage()
    {
        return $this->page > 1;
    }

    public function getCurrentPage()
    {
        return $this->page;
    }

    protected function fetchPage($key = null)
    {
        $page = isset($_GET[$key]) ? (int)$_GET[$key] : 1;
        if ($page <= 0) {
            return $page = 1;
        } elseif ($page > $this->getPagesCount()) {
            return $page = $this->getPagesCount();
        } else {
            return $page;
        }
    }

    public function getPage()
    {
        if (!$this->page) {
            $this->page = $this->fetchPage($this->getPageKey());
        }
        return $this->page;
    }

    public function setPage($page)
    {
        return $this->page = $page;
    }

    /**
     * Apply limits to source
     * @throws \Mindy\Exception\Exception
     * @return $this
     */
    public function paginate()
    {
        if (is_array($this->source)) {
            return $this->applyLimitArray();
        } else if ($this->source instanceof \Mindy\Orm\Manager) {
            $this->source = $this->source->getQuerySet();
            return $this->applyLimitQuerySet();
        } else if ($this->source instanceof \Mindy\Orm\QuerySet) {
            return $this->applyLimitQuerySet();
        } else if ($this->source instanceof \Mindy\Query\Query) {
            return $this->applyLimitQuery();
        } else if ($this->source instanceof IPagination) {
            return $this->applyLimitByInterface();
        } else {
            throw new Exception("Unknown source");
        }
    }

    /**
     * @return array
     */
    protected function applyLimitArray()
    {
        $this->total = count($this->source);
        $page = $this->getPage();
        $pageSize = $this->getPageSize();
        $this->data = array_slice($this->source, $pageSize * ($page <= 1 ? 0 : $page - 1), $pageSize);
        return $this->data;
    }

    /**
     * @return array
     */
    protected function applyLimitQuery()
    {
        $this->total = $this->source->count();
        $page = $this->getPage();
        $pageSize = $this->getPageSize();
        $offset = $page > 1 ? $pageSize * ($page - 1) : 0;
        $this->data = $this->source->limit($pageSize)->offset($offset)->all();
        return $this->data;
    }

    /**
     * @return array
     */
    protected function applyLimitQuerySet()
    {
        $source = clone $this->source;
        $this->total = $source->count();
        $this->data = $this->source->paginate($this->getPage(), $this->getPageSize())->all();
        return $this->data;
    }

    /**
     * @return array
     */
    protected function applyLimitByInterface()
    {
        $this->total = $this->source->getTotal();
        $page = $this->getPage();
        $pageSize = $this->getPageSize();
        $offset = $page > 1 ? $pageSize * ($page - 1) : 0;
        $this->source->setLimit($pageSize);
        $this->source->setOffset($offset);
        $this->data = $this->source->all();
        return $this->data;
    }

    public function setPageKey($key)
    {
        $this->pageKey = $key;
    }

    public function getPageKey()
    {
        if ($this->pageKey === null) {
            if ($this->isQs) {
                $base = $this->source->model->classNameShort();
            } else {
                $base = 'Pager';
            }

            return $base . '_' . $this->id;
        } else {
            return $this->pageKey;
        }
    }

    public function iterPrevPage($count = 3)
    {
        if ($this->getCurrentPage() == $this->getPagesCount() && $this->getPagesCount() - $count * 2 > 0) {
            $count *= 2;
        }
        $pages = [];
        foreach (array_reverse(range(1, $count)) as $i) {
            $page = $this->getCurrentPage() - $i;
            if ($page > 0) {
                $pages[] = $page;
            }
        }
        return $pages;
    }

    public function iterNextPage($count = 3)
    {
        if ($this->getCurrentPage() == 1 && $this->getPagesCount() >= $count * 2) {
            $count *= 2;
        }
        $pages = [];
        foreach (range(1, $count) as $i) {
            $page = $this->getCurrentPage() + $i;
            if ($page <= $this->getPagesCount()) {
                $pages[] = $page;
            }
        }
        return $pages;
    }

    public function serialize()
    {
        $props = Creator::getObjectVars($this);
        return serialize($props);
    }

    public function unserialize($data)
    {
        $props = unserialize($data);
        Creator::configure($this, $props);
    }
}
