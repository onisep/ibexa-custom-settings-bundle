<?php

declare(strict_types=1);

namespace Onisep\IbexaCustomSettingsBundle\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Onisep\IbexaCustomSettingsBundle\Entity\LocationSetting;

/**
 * @extends ServiceEntityRepository<LocationSetting>
 */
class LocationSettingRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, LocationSetting::class);
    }

    /**
     * @return LocationSetting[]
     */
    public function findAllFiltered(?string $key = null): array
    {
        $queryBuilder = $this->createQueryBuilder('l')
            ->orderBy('l.id', 'ASC');

        if ($key !== null && $key !== '') {
            $queryBuilder->where('l.key = :key')
                ->setParameter('key', $key);
        }

        return $queryBuilder->getQuery()->getResult();
    }

    /**
     * @param int[] $locationIds
     * @return array|false
     */
    public function findByKeyAndLocationId(string $key, array $locationIds, bool $firstOnly = false): array|false
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
     * @return LocationSetting[]
     */
    public function findByLocationIds(array $locationIds): array
    {
        if (empty($locationIds)) {
            return [];
        }

        return $this->createQueryBuilder('l')
            ->andWhere('l.locationId in (:ids)')
            ->setParameter('ids', $locationIds)
            ->orderBy('l.id', 'ASC')
            ->getQuery()
            ->getResult();
    }
}
