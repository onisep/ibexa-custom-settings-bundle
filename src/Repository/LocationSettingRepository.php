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
     * @return LocationSetting|LocationSetting[]|null
     */
    public function findByKeyAndLocationId(string $key, array $locationIds, bool $firstOnly = false)
    {
        $query = $this->createQueryBuilder('l')
            ->andWhere('l.key = :key')
            ->setParameter('key', $key)
            ->andWhere('l.locationId in (:ids)')
            ->setParameter('ids', $locationIds)
            ->orderBy('l.id', 'ASC')
            ->getQuery();

        if ($firstOnly) {
            return $query->getOneOrNullResult();
        }

        return $query->getResult();
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
