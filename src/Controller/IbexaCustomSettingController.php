<?php

namespace Onisep\IbexaCustomSettingsBundle\Controller;

use eZ\Bundle\EzPublishCoreBundle\Controller;
use Onisep\IbexaCustomSettingsBundle\Entity\LocationSetting;
use Onisep\IbexaCustomSettingsBundle\Repository\LocationSettingRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class IbexaCustomSettingController extends Controller
{
    private LocationSettingRepository $locationSettingRepository;

    public function __construct(LocationSettingRepository $locationSettingRepository)
    {
        $this->locationSettingRepository = $locationSettingRepository;
    }

    public function index(Request $request): Response
    {
        $keyFilter = $request->query->get('key');

        // All settings filterer by key
        $settings = $this->locationSettingRepository->findAllFiltered($keyFilter);
        $settingsGroupedById = array_reduce($settings, static function (array $accumulator, LocationSetting $setting) {
            $accumulator[$setting->getLocationId()][] = $setting;

            return $accumulator;
        }, []);

        // Settings keys only (for filter)
        $settingsKeys = array_map(static function (LocationSetting $setting) {
            return $setting->getKey();
        }, $this->locationSettingRepository->findAllFiltered());
        sort($settingsKeys);

        return $this->render('@IbexaCustomSettings/index.html.twig', [
            'locations_settings' => $settingsGroupedById,
            'settings_keys' => array_unique($settingsKeys),
        ]);
    }
}
