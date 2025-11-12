<?php

declare(strict_types=1);

namespace Onisep\IbexaCustomSettingsBundle\Tab;

use Doctrine\ORM\EntityManagerInterface;
use Ibexa\Contracts\AdminUi\Tab\AbstractEventDispatchingTab;
use Ibexa\Contracts\AdminUi\Tab\OrderedTabInterface;
use Ibexa\Contracts\Core\Repository\LocationService;
use Ibexa\Contracts\Core\Repository\Values\Content\Location;
use Ibexa\Core\MVC\Symfony\Security\Authorization\Attribute;
use Onisep\IbexaCustomSettingsBundle\Entity\LocationSetting;
use Onisep\IbexaCustomSettingsBundle\Form\LocationSettingsType;
use Onisep\IbexaCustomSettingsBundle\Repository\LocationSettingRepository;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Contracts\Translation\TranslatorInterface;
use Twig\Environment;

class IbexaCustomSettingsLocationTab extends AbstractEventDispatchingTab implements OrderedTabInterface
{
    public function __construct(
        Environment $twigEnvironment,
        TranslatorInterface $translator,
        EventDispatcherInterface $eventDispatcher,
        private readonly FormFactoryInterface $formFactory,
        private readonly AuthorizationCheckerInterface $authorizationChecker,
        private readonly EntityManagerInterface $entityManager,
        private readonly RequestStack $requestStack,
        private readonly LocationSettingRepository $locationSettingRepository,
        private readonly LocationService $locationService
    ) {
        parent::__construct($twigEnvironment, $translator, $eventDispatcher);
    }

    public function getTemplate(): string
    {
        return '@IbexaCustomSettings/tab.html.twig';
    }

    public function getTemplateParameters(array $contextParameters = []): array
    {
        /** @var Location $location */
        $location = $contextParameters['location'];
        $request = $this->requestStack->getCurrentRequest();
        $settings = $this->locationSettingRepository->findByLocationId($location->id);

        $form = $this->formFactory->create(LocationSettingsType::class, [
            'settings' => $settings,
            'can_edit_keys' => $this->authorizationChecker->isGranted(new Attribute('ibexa_custom_settings', 'key_edit')),
            'can_edit_values' => $this->authorizationChecker->isGranted(new Attribute('ibexa_custom_settings', 'value_edit')),
        ]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Add & Update settings
            foreach ($form->getData() as $item) {
                if (is_iterable($item)) {
                    foreach ($item as $subItem) {
                        if ($subItem instanceof LocationSetting && $subItem->getLocationId() === null) {
                            $enr = true;
                            $subItem->setLocationId($location->id);
                            $locationSettingList = $this->locationSettingRepository->findAllFiltered($subItem->getKey());
                            foreach ($locationSettingList as $locationSettingItem) {
                                if ($locationSettingItem->getValue() === $subItem->getValue()) {
                                    $enr = false;
                                }
                            }

                            if ($enr) {
                                $this->entityManager->persist($subItem);
                            }
                        }
                    }
                }
            }

            // Search settings to remove
            if (array_key_exists('settings', $form->getData())) {
                foreach ($settings as $setting) {
                    $found = false;
                    foreach ($form->getData()['settings'] as $updatedSetting) {
                        if ($updatedSetting && $updatedSetting->getId() === $setting->getId()) {
                            $found = true;
                        }
                    }

                    if (!$found) {
                        $this->entityManager->remove($setting);
                    }
                }

                $this->entityManager->flush();
            }

            return [];
        }

        $parentLocationIds = $location->path;
        unset($parentLocationIds[$location->id]);
        $parentValues = $this->locationSettingRepository->findByLocationIds(array_slice($parentLocationIds, 0, -1));

        $locations = [];
        foreach ($parentValues as $parentValue) {
            $locationId = $parentValue->getLocationId();
            if (!array_key_exists($locationId, $locations)) {
                $locations[$locationId] = $this->locationService->loadLocation($locationId);
            }
        }

        return [
            'form' => $form->createView(),
            'parent_values' => $parentValues,
            'locations' => $locations
        ];
    }

    public function getOrder(): int
    {
        return 800;
    }

    public function getIdentifier(): string
    {
        return 'ibexa-settings';
    }

    public function getName(): string
    {
        return 'Paramètres';
    }
}
