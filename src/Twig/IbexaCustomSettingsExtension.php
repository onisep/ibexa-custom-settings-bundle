<?php

namespace Onisep\IbexaCustomSettingsBundle\Twig;

use eZ\Publish\API\Repository\LocationService;
use eZ\Publish\API\Repository\Values\Content\Content as ContentAPI;
use eZ\Publish\API\Repository\Values\Content\Location;
use eZ\Publish\Core\Repository\Values\Content\Content;
use Onisep\IbexaCustomSettingsBundle\Entity\LocationSetting;
use Onisep\IbexaCustomSettingsBundle\Repository\LocationSettingRepository;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class IbexaCustomSettingsExtension extends AbstractExtension
{
    private LocationService $locationService;
    private LocationSettingRepository $locationSettingRepository;

    public function __construct(LocationService $locationService, LocationSettingRepository $locationSettingRepository)
    {
        $this->locationService = $locationService;
        $this->locationSettingRepository = $locationSettingRepository;
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('get_location_setting', [$this, 'getSettingByLocation']),
            new TwigFunction('get_location_settings', [$this, 'getSettingsByLocation']),
        ];
    }

    public function getSettingByLocation(string $key, $locationOrLocationId)
    {
        $locationIds = $this->getLocationIds($locationOrLocationId);
        $setting = $this->locationSettingRepository->findByKeyAndLocationId($key, $locationIds, true);

        if (!$setting) {
            return null;
        }

        return $setting->getValue();
    }

    public function getSettingsByLocation($locationOrLocationId): array
    {
        $locationIds = $this->getLocationIds($locationOrLocationId);
        $settings = $this->locationSettingRepository->findByLocationIds($locationIds);

        return array_reduce($settings, static function (array $result, LocationSetting $setting) {
            $result[$setting->getKey()] = $setting->getValue();

            return $result;
        }, []);
    }

    private function getLocationIds($locationOrLocationId): array
    {
        if ($locationOrLocationId instanceof Content || $locationOrLocationId instanceof ContentAPI) {
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
