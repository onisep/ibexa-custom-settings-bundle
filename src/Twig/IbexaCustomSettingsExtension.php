<?php

declare(strict_types=1);

namespace Onisep\IbexaCustomSettingsBundle\Twig;

use Ibexa\Contracts\Core\Repository\Values\Content\Content;
use Ibexa\Contracts\Core\Repository\Values\Content\Location;
use Onisep\IbexaCustomSettingsBundle\Entity\LocationSetting;
use Onisep\IbexaCustomSettingsBundle\Repository\LocationSettingRepository;
use Twig\Attribute\AsTwigFunction;

class IbexaCustomSettingsExtension
{
    public function __construct(private readonly LocationSettingRepository $locationSettingRepository)
    {
    }

    #[AsTwigFunction('get_location_setting')]
    public function getSettingByLocation(string $key, Location|int|array $locationOrLocationId): ?string
    {
        $locationIds = $this->getLocationIds($locationOrLocationId);
        $setting = $this->locationSettingRepository->findByKeyAndLocationId($key, $locationIds, true);

        return $setting ? $setting['setting_value'] : null;
    }

    #[AsTwigFunction('get_location_settings')]
    public function getSettingsByLocation(Location|int|array $locationOrLocationId): array
    {
        $locationIds = $this->getLocationIds($locationOrLocationId);
        $settings = $this->locationSettingRepository->findByLocationIds($locationIds);

        $result = [];
        foreach ($settings as $setting) {
            $result[$setting->getKey()] = $setting->getValue();
        }

        return $result;
    }

    /**
     * @return int[]
     */
    private function getLocationIds(Location|int|array $locationOrLocationId): array
    {
        if ($locationOrLocationId instanceof Location) {
            return [$locationOrLocationId->id];
        }

        if (is_array($locationOrLocationId)) {
            return $locationOrLocationId;
        }

        return [$locationOrLocationId];
    }
}
