<?php

declare(strict_types=1);

namespace Onisep\IbexaCustomSettingsBundle\Controller;

use Ibexa\Bundle\Core\Controller;
use Ibexa\Contracts\Core\Repository\LocationService;
use Onisep\IbexaCustomSettingsBundle\Entity\LocationSetting;
use Onisep\IbexaCustomSettingsBundle\Repository\LocationSettingRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class IbexaCustomSettingController extends Controller
{
    public function __construct(private readonly LocationSettingRepository $locationSettingRepository, private readonly LocationService $locationService)
    {
    }

    public function index(Request $request): Response
    {
        $keyFilter = $request->query->get('key');

        // All settings filterer by key
        $settings = $this->locationSettingRepository->findAllFiltered($keyFilter);
        $locations = [];
        $settingsGroupedById = array_reduce($settings, function (array $accumulator, LocationSetting $locationSetting) use (&$locations): array {
            $locationId = $locationSetting->getLocationId();
            $accumulator[$locationId][] = $locationSetting;

            if (!array_key_exists($locationId, $locations)) {
                $locations[$locationId] = $this->locationService->loadLocation($locationId);
            }

            return $accumulator;
        }, []);

        // Settings keys only (for filter)
        $settingsKeys = array_map(static fn(LocationSetting $locationSetting): string => $locationSetting->getKey(), $this->locationSettingRepository->findAllFiltered());
        sort($settingsKeys);

        return $this->render('@IbexaCustomSettings/index.html.twig', [
            'locations_settings' => $settingsGroupedById,
            'locations' => $locations,
            'settings_keys' => array_unique($settingsKeys),
        ]);
    }
}
