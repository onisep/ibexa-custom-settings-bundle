<?php

namespace Onisep\IbexaCustomSettingsBundle\Controller;

use Ibexa\Bundle\Core\Controller;
use Ibexa\Contracts\Core\Repository\LocationService;
use Onisep\IbexaCustomSettingsBundle\Entity\LocationSetting;
use Onisep\IbexaCustomSettingsBundle\Repository\LocationSettingRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class IbexaCustomSettingController extends Controller
{
    private LocationSettingRepository $locationSettingRepository;
    private LocationService $locationService;

    public function __construct(LocationSettingRepository $locationSettingRepository, LocationService $locationService)
    {
        $this->locationSettingRepository = $locationSettingRepository;
        $this->locationService = $locationService;
    }

    public function index(Request $request): Response
    {
        $keyFilter = $request->query->get('key');

        // All settings filterer by key
        $settings = $this->locationSettingRepository->findAllFiltered($keyFilter);
        $locations = [];
        $settingsGroupedById = array_reduce($settings, function (array $accumulator, LocationSetting $setting) use (&$locations) {
            $locationId = $setting->getLocationId();
            $accumulator[$locationId][] = $setting;

            if (!array_key_exists($locationId, $locations)) {
                $locations[$locationId] = $this->locationService->loadLocation($locationId);
            }

            return $accumulator;
        }, []);

        // Settings keys only (for filter)
        $settingsKeys = array_map(static function (LocationSetting $setting) {
            return $setting->getKey();
        }, $this->locationSettingRepository->findAllFiltered());
        sort($settingsKeys);

        return $this->render('@IbexaCustomSettings/index.html.twig', [
            'locations_settings' => $settingsGroupedById,
            'locations' => $locations,
            'settings_keys' => array_unique($settingsKeys),
        ]);
    }
}
