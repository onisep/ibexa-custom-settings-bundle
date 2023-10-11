<?php

namespace Onisep\IbexaCustomSettingsBundle\EventSubscriber;

use eZ\Publish\API\Repository\PermissionResolver;
use EzSystems\EzPlatformAdminUi\Menu\Event\ConfigureMenuEvent;
use EzSystems\EzPlatformAdminUi\Menu\MainMenuBuilder;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class MenuSubscriber implements EventSubscriberInterface
{
    private PermissionResolver $permissionResolver;

    public function __construct(PermissionResolver $permissionResolver)
    {
        $this->permissionResolver = $permissionResolver;
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
                'label' => 'ibexa_custom_settings.menu.label',
                'route' => 'ibexa_custom_settings_index',
            ]);
        }
    }
}
