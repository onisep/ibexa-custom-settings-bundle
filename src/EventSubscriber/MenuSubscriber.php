<?php

namespace Onisep\IbexaCustomSettingsBundle\EventSubscriber;

use eZ\Publish\API\Repository\PermissionResolver;
use EzSystems\EzPlatformAdminUi\Menu\Event\ConfigureMenuEvent;
use EzSystems\EzPlatformAdminUi\Menu\MainMenuBuilder;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

class MenuSubscriber implements EventSubscriberInterface
{
    private PermissionResolver $permissionResolver;
    private TranslatorInterface $translator;

    public function __construct(PermissionResolver $permissionResolver, TranslatorInterface $translator)
    {
        $this->permissionResolver = $permissionResolver;
        $this->translator = $translator;
    }

    public static function getSubscribedEvents(): array
    {
        return [ConfigureMenuEvent::MAIN_MENU => ['configureMenu']];
    }

    public function configureMenu(ConfigureMenuEvent $event)
    {
        $menu = $event->getMenu();
        if (!isset($menu[MainMenuBuilder::ITEM_ADMIN])) {
            return;
        }

        if ($this->permissionResolver->hasAccess('ibexa_custom_settings', 'key_edit')) {
            $menu[MainMenuBuilder::ITEM_ADMIN]->addChild('ibexa_custom_settings', [
                'label' => $this->translator->trans('ibexa_custom_settings.menu.label'),
                'route' => 'ibexa_custom_settings_index',
            ]);
        }
    }
}
