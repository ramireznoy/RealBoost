<?php

namespace CoreBundle\Repository;

/**
 * ServiceRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class ServiceRepository extends \Doctrine\ORM\EntityRepository
{
    public function findDuplicates($array) {
        $qb = $this->createQueryBuilder('u')
                ->where('u.name = :name')
                ->andWhere('u.id <> :id')
                ->setParameter('name', $array['name'])
                ->setParameter('id', $array['id']);
        return $qb->getQuery()->getResult();
    }
    
    public function deleteByIds($array) {
        $qb = $this->createQueryBuilder('u')
                ->delete()
                ->where('u.id in (:list)')
                ->setParameter('list', $array);
        return $qb->getQuery()->getSingleScalarResult();
    }
}
