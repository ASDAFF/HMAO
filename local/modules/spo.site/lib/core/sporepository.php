<?php
namespace Spo\Site\Core;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\NoResultException;
use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\AbstractQuery;
use Spo\Site\Helpers\PagingHelper;
use Spo\Site\Util\CVarDumper;
use Doctrine\ORM\Tools\Pagination\Paginator;

class SPORepository extends EntityRepository
{

	/**
	 * @var QueryBuilder $queryBuilder
	 */
	public $queryBuilder;


	public function range($start, $limit)
	{
        if($start > 0){
            $this->queryBuilder->setFirstResult($start);
        }
        if($limit > 0){
            $this->queryBuilder->setMaxResults($limit);
        }

        return $this;
	}

    public function paging(PagingHelper $paging)
    {
        $this->range($paging->getStart(), $paging->getLimit());
        return $this;
    }

    public function all($hydrationType = AbstractQuery::HYDRATE_OBJECT)
    {
        try {
            return $this->queryBuilder->getQuery()->getResult($hydrationType);
        } catch (NoResultException $e) {
            return null;
        }
    }

	public function one($hydrationType = AbstractQuery::HYDRATE_OBJECT)
	{
		// todo добавить NotUniqueResultException?
		try {
			return $this->queryBuilder->getQuery()->getSingleResult($hydrationType);
		} catch (NoResultException $e) {
			return null;
		}
	}

    public function count()
    {
        $pagination = new Paginator($this->queryBuilder);
        return count($pagination);
    }

}