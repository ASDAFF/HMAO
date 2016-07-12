<?php
/**
 * Created by PhpStorm.
 * User: dizinfector
 * Date: 30.04.15
 * Time: 15:00
 */
namespace Spo\Site\Helpers;

class PagingHelper
{
    public static $defaultPageSize = 25;
    protected $pageParam = '';
    protected $pageSize = 0;
    protected $limit = -1;
    protected $start = 0;

    public function __construct($autoInit = true, $pageSize = 0, $pageParam = 'page')
    {
        $this
            ->setPageParam($pageParam)
            ->setPageSize($pageSize > 0 ? $pageSize : self::$defaultPageSize);

        if($autoInit){
            $this->initPagingParams();
        }
    }

    protected function initPagingParams()
    {
        $page = $this->getCurrentPage();

        $this
            ->setStart(($page-1) * $this->pageSize)
            ->setLimit($this->pageSize);

        return $this;
    }

    public function getCurrentPage()
    {
        $request = \Bitrix\Main\Application::getInstance()->getContext()->getRequest();

        $page = $request->get($this->pageParam);

        if($page === null){
            $page = 1;
        }else{
            $page = (intval($page) > 0) ? intval($page) : 1;
        }
        return $page;
    }

    public function getStart()
    {
        return $this->start;
    }
    public function setStart($start)
    {
        $this->start = intval($start);
        return $this;
    }

    public function getLimit()
    {
        return $this->limit;
    }
    public function setLimit($limit)
    {
        $this->limit = intval($limit);
        return $this;
    }

    public function getPageSize()
    {
        return $this->pageSize;
    }
    public function setPageSize($pageSize)
    {
        $this->pageSize = intval($pageSize);
        return $this;
    }

    public function getPageParam()
    {
        return $this->pageParam;
    }
    public function setPageParam($pageParam)
    {
        $this->pageParam = $pageParam;
        return $this;
    }

    public function getPageCountByTotalRecordCount($totalCount)
    {
        return ceil($totalCount/$this->pageSize);
    }
}