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
    public function getSettingByLocation(string $key, $locationOrLocationId)
    {
        $locationIds = $this->getLocationIds($locationOrLocationId);
        $setting = $this->locationSettingRepository->findByKeyAndLocationId($key, $locationIds, true);

        if (!$setting) {
            return null;
        }

        return $setting["setting_value"];
    }

    #[AsTwigFunction('get_location_settings')]
    public function getSettingsByLocation($locationOrLocationId): array
    {
        $locationIds = $this->getLocationIds($locationOrLocationId);
        $settings = $this->locationSettingRepository->findByLocationIds($locationIds);

        return array_reduce($settings, static function (array $result, LocationSetting $locationSetting): array {
            $result[$locationSetting->getKey()] = $locationSetting->getValue();

            return $result;
        }, []);
    }

    private function getLocationIds($locationOrLocationId): array
    {
        if ($locationOrLocationId instanceof Content) {
            throw new \InvalidArgumentException('The locationOrLocationId argument must be a location or a location ID.');
        }

        // Location object
        if ($locationOrLocationId instanceof Location) {
            return [$locationOrLocationId->id];
        }

        // Location path or array of ids
        if (is_array($locationOrLocationId)) {
            return $locationOrLocationId;
        }

        // Location id
        return [$locationOrLocationId];
    }
}
