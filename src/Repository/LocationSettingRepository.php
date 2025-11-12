<?php

declare(strict_types=1);

namespace Onisep\IbexaCustomSettingsBundle\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Doctrine\Persistence\ManagerRegistry;
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
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, LocationSetting::class);
    }

    /**
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function add(LocationSetting $locationSetting, bool $flush = true): void
    {
        $this->_em->persist($locationSetting);

        if ($flush) {
            $this->_em->flush();
        }
    }

    /**
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function remove(LocationSetting $locationSetting, bool $flush = true): void
    {
        $this->_em->remove($locationSetting);

        if ($flush) {
            $this->_em->flush();
        }
    }

    /**
     * @return \Onisep\IbexaCustomSettingsBundle\Entity\LocationSetting[]
     */
    public function findAllFiltered(string $key = null): array
    {
        $queryBuilder = $this->createQueryBuilder('l')
            ->orderBy('l.id', 'ASC');

        if ($key !== null && $key !== '') {
            $queryBuilder
                ->where('l.key = :key')
                ->setParameter('key', $key);
        }

        return $queryBuilder->getQuery()->getResult();
    }

    /**
     *
     * @return array|false
     */
    public function findByKeyAndLocationId(string $key, array $locationIds, bool $firstOnly = false)
    {
        $queryBuilder = $this->getEntityManager()->getConnection()->createQueryBuilder()
            ->select('l.*, e.depth')
            ->from('ibexa_custom_settings', 'l')
            ->andWhere('l.setting_key = :key')
            ->setParameter('key', $key)
            ->andWhere($this->getEntityManager()->getExpressionBuilder()->in('l.location_id', $locationIds))
            ->join('l', 'ibexa_content_tree', 'e', 'l.location_id = e.node_id')
            ->groupBy('setting_key')
            ->orderBy('l.id', 'ASC')
            ->addOrderBy('e.depth', 'DESC');

        if ($firstOnly) {
            return $queryBuilder->fetchAssociative();
        }

        return $queryBuilder->fetchAllAssociative();
    }

    /**
     * @return \Onisep\IbexaCustomSettingsBundle\Entity\LocationSetting[]
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
     * @return \Onisep\IbexaCustomSettingsBundle\Entity\LocationSetting[]
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
