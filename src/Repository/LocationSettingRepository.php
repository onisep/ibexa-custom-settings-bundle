<?php

namespace Onisep\IbexaCustomSettingsBundle\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Onisep\IbexaCustomSettingsBundle\Entity\LocationSetting;

/**
 * @extends ServiceEntityRepository<LocationSetting>
 *
 * @method LocationSetting|null find($id, $lockMode = null, $lockVersion = null)
 * @method LocationSetting|null findOneBy(array $criteria, array $orderBy = null)
 * @method LocationSetting[]    findAll()
 * @method LocationSetting[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class LocationSettingRepository extends ServiceEntityRepository
{
    /**
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function add(LocationSetting $entity, bool $flush = true): void
    {
        $this->_em->persist($entity);

        if ($flush) {
            $this->_em->flush();
        }
    }

    /**
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function remove(LocationSetting $entity, bool $flush = true): void
    {
        $this->_em->remove($entity);

        if ($flush) {
            $this->_em->flush();
        }
    }

    /**
     * @return LocationSetting[]
     */
    public function findAllFiltered(string $key = null): array
    {
        $query = $this->createQueryBuilder('l')
            ->orderBy('l.id', 'ASC');

        if ($key !== null && $key !== '') {
            $query
                ->where('l.key = :key')
                ->setParameter('key', $key);
        }

        return $query->getQuery()->getResult();
    }

    /**
     * @param string $key
     * @param array $locationIds
     * @param bool $firstOnly
     *
     * @return array|false
     */
    public function findByKeyAndLocationId(string $key, array $locationIds, bool $firstOnly = false)
    {
        $query = $this->getEntityManager()->getConnection()->createQueryBuilder()
            ->select('l.*, e.depth')
            ->from('ibexa_custom_settings', 'l')
            ->andWhere('l.setting_key = :key')
            ->setParameter('key', $key)
            ->andWhere($this->getEntityManager()->getExpressionBuilder()->in('l.location_id', $locationIds))
            ->join('l', 'ezcontentobject_tree', 'e', 'l.location_id = e.node_id')
            ->groupBy('setting_key')
            ->orderBy('l.id', 'ASC')
            ->addOrderBy('e.depth', 'DESC');

        if ($firstOnly) {
            return $query->execute()->fetchAssociative();
        }

        return $query->execute()->fetchAllAssociative();
    }

    /**
     * @param int $locationId
     *
     * @return LocationSetting[]
     */
    public function findByLocationId(int $locationId): array
    {
        return $this->createQueryBuilder('l')
            ->andWhere('l.locationId = :id')
            ->setParameter('id', $locationId)
            ->orderBy('l.id', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /**
     * @param int[] $locationIds
     *
     * @return LocationSetting[]
     */
    public function findByLocationIds(array $locationIds): array
    {
        return $this->createQueryBuilder('l')
            ->andWhere('l.locationId in (:ids)')
            ->setParameter('ids', $locationIds)
            ->orderBy('l.id', 'ASC')
            ->getQuery()
            ->getResult();
    }
}
