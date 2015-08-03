<?php
namespace Foxsiscom\BaseBundle\Entity\Repository;

use Doctrine\ORM\EntityRepository;

abstract class EntityRepositoryAbstract extends EntityRepository
{

    /**
     *
     * @param array $criteria
     * @return QueryBuilder
     */
    public function findByCriteria($criteria = array())
    {
        $qb = $this->createQueryBuilder('e');
        $qb->select('e');

        foreach ($criteria as $key => $value) {
            if (! $this->getClassMetadata()->hasField($key) && ! $this->getClassMetadata()->hasAssociation($key)) {
                continue;
            } elseif (is_string($value)) {
                $value = '%' . str_replace(' ', '%', trim($value)) . '%';
                $qb->andWhere($qb->expr()
                    ->like("lower(e.$key)", "lower(:$key)"));
            } else {
                $qb->andWhere("e.$key = :$key");
            }
            $qb->setParameter($key, $value);
        }

        return $qb;
    }
}
