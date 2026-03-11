<?php

declare(strict_types=1);

namespace Onisep\IbexaCustomSettingsBundle\Tab;

use Ibexa\Contracts\AdminUi\Tab\AbstractEventDispatchingTab;
use Ibexa\Contracts\AdminUi\Tab\OrderedTabInterface;
use Ibexa\Contracts\Core\Repository\LocationService;
use Ibexa\Contracts\Core\Repository\Values\Content\Location;
use Ibexa\Core\MVC\Symfony\Security\Authorization\Attribute;
use Onisep\IbexaCustomSettingsBundle\Form\LocationSettingsType;
use Onisep\IbexaCustomSettingsBundle\Repository\LocationSettingRepository;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Routing\RouterInterface;
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
        private readonly LocationSettingRepository $locationSettingRepository,
        private readonly LocationService $locationService,
        private readonly RouterInterface $router,
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
        $settings = $this->locationSettingRepository->findByLocationId($location->id);

        $form = $this->formFactory->create(LocationSettingsType::class, [
            'settings' => $settings,
            'can_edit_keys' => $this->authorizationChecker->isGranted(new Attribute('ibexa_custom_settings', 'key_edit')),
            'can_edit_values' => $this->authorizationChecker->isGranted(new Attribute('ibexa_custom_settings', 'value_edit')),
        ], [
            'action' => $this->router->generate('ibexa_custom_settings_save', [
                'locationId' => $location->id,
            ]),
        ]);

        // Inherited settings from parent locations
        $parentLocationIds = array_slice($location->path, 0, -1);
        $parentValues = $this->locationSettingRepository->findByLocationIds($parentLocationIds);

        $locations = [];
        foreach ($parentValues as $parentValue) {
            $locationId = $parentValue->getLocationId();
            if (!isset($locations[$locationId])) {
                $locations[$locationId] = $this->locationService->loadLocation($locationId);
            }
        }

        return [
            'form' => $form->createView(),
            'parent_values' => $parentValues,
            'locations' => $locations,
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
