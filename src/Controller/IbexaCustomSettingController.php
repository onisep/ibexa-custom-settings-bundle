<?php

declare(strict_types=1);

namespace Onisep\IbexaCustomSettingsBundle\Controller;

use Doctrine\ORM\EntityManagerInterface;
use Ibexa\Bundle\Core\Controller;
use Ibexa\Contracts\Core\Repository\LocationService;
use Ibexa\Core\MVC\Symfony\Security\Authorization\Attribute;
use Onisep\IbexaCustomSettingsBundle\Entity\LocationSetting;
use Onisep\IbexaCustomSettingsBundle\Form\LocationSettingsType;
use Onisep\IbexaCustomSettingsBundle\Repository\LocationSettingRepository;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

class IbexaCustomSettingController extends Controller
{
    public function __construct(
        private readonly LocationSettingRepository $locationSettingRepository,
        private readonly LocationService $locationService,
        private readonly EntityManagerInterface $entityManager,
        private readonly AuthorizationCheckerInterface $authorizationChecker,
    ) {
    }

    public function index(Request $request): Response
    {
        $keyFilter = $request->query->get('key');

        $settings = $this->locationSettingRepository->findAllFiltered($keyFilter);

        $locationIds = array_unique(array_map(
            static fn(LocationSetting $s): int => $s->getLocationId(),
            $settings,
        ));

        $locations = [];
        foreach ($locationIds as $locationId) {
            $locations[$locationId] = $this->locationService->loadLocation($locationId);
        }

        $settingsGroupedById = [];
        foreach ($settings as $setting) {
            $settingsGroupedById[$setting->getLocationId()][] = $setting;
        }

        if ($keyFilter === null || $keyFilter === '') {
            $settingsKeys = array_unique(array_map(
                static fn(LocationSetting $s): string => $s->getKey(),
                $settings,
            ));
            sort($settingsKeys);
        } else {
            $allSettings = $this->locationSettingRepository->findAllFiltered();
            $settingsKeys = array_unique(array_map(
                static fn(LocationSetting $s): string => $s->getKey(),
                $allSettings,
            ));
            sort($settingsKeys);
        }

        return $this->render('@IbexaCustomSettings/index.html.twig', [
            'locations_settings' => $settingsGroupedById,
            'locations' => $locations,
            'settings_keys' => $settingsKeys,
        ]);
    }

    public function save(Request $request, int $locationId): RedirectResponse
    {
        $existingSettings = $this->locationSettingRepository->findByLocationId($locationId);

        $form = $this->container->get('form.factory')->create(LocationSettingsType::class, [
            'settings' => $existingSettings,
            'can_edit_keys' => $this->authorizationChecker->isGranted(new Attribute('ibexa_custom_settings', 'key_edit')),
            'can_edit_values' => $this->authorizationChecker->isGranted(new Attribute('ibexa_custom_settings', 'value_edit')),
        ]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            $submittedSettings = $data['settings'] ?? [];

            // Persist new & updated settings
            foreach ($submittedSettings as $setting) {
                if (!$setting instanceof LocationSetting) {
                    continue;
                }

                if ($setting->getLocationId() === null) {
                    $setting->setLocationId($locationId);
                }

                $this->entityManager->persist($setting);
            }

            // Remove deleted settings
            $submittedIds = array_filter(array_map(
                static fn(?LocationSetting $s): ?int => $s?->getId(),
                $submittedSettings,
            ));

            foreach ($existingSettings as $existing) {
                if (!in_array($existing->getId(), $submittedIds, true)) {
                    $this->entityManager->remove($existing);
                }
            }

            $this->entityManager->flush();
        }

        return new RedirectResponse(
            $this->generateUrl('ibexa.url.alias', ['locationId' => $locationId])
            . '#ibexa-tab-location-view-ibexa-settings#tab'
        );
    }
}
