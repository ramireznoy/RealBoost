<?php

namespace AdminBundle\Repository;

/**
 * SystemUserRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class SystemUserRepository extends \Doctrine\ORM\EntityRepository
{
    public function findDuplicates($array) {
        $qb = $this->createQueryBuilder('u')
                ->where('u.username = :username')
                ->orWhere('u.phone = :phone')
                ->orWhere('u.email = :email')
                ->andWhere('u.id <> :id')
                ->setParameter('username', $array['username'])
                ->setParameter('email', $array['email'])
                ->setParameter('phone', $array['phone'])
                ->setParameter('id', $array['id']);
        return $qb->getQuery()->getResult();
    }
    
    public function findDuplicatedUsername($username) {
        $qb = $this->createQueryBuilder('u')
                ->where('u.username = :username')
                ->setParameter('username', $username);
        return $qb->getQuery()->getResult();
    }
    
    public function findDuplicatedPhone($phone) {
        $qb = $this->createQueryBuilder('u')
                ->where('u.phone = :phone')
                ->setParameter('phone', $phone);
        return $qb->getQuery()->getResult();
    }
    
    public function setDisabledByIds($array) {
        $qb = $this->createQueryBuilder('u')
                ->update()
                ->set('u.enabled', 'false')
                ->where('u.id in (:list)')
                ->setParameter('list', $array);
        return $qb->getQuery()->getSingleScalarResult();
    }
}
