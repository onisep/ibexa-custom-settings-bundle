<?php

declare(strict_types=1);

namespace Onisep\IbexaCustomSettingsBundle\EventSubscriber;

use Ibexa\Contracts\Core\Repository\PermissionResolver;
use Ibexa\AdminUi\Menu\Event\ConfigureMenuEvent;
use Ibexa\AdminUi\Menu\MainMenuBuilder;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class MenuSubscriber implements EventSubscriberInterface
{
    public function __construct(private readonly PermissionResolver $permissionResolver)
    {
    }

    public static function getSubscribedEvents(): array
    {
        return [ConfigureMenuEvent::MAIN_MENU => ['configureMenu']];
    }

    public function configureMenu(ConfigureMenuEvent $configureMenuEvent): void
    {
        $menu = $configureMenuEvent->getMenu();
        if (!isset($menu[MainMenuBuilder::ITEM_ADMIN])) {
            return;
        }

        if ($this->permissionResolver->hasAccess('ibexa_custom_settings', 'key_edit')) {
            $menu[MainMenuBuilder::ITEM_ADMIN]->addChild('ibexa_custom_settings', [
                'label' => 'ibexa_custom_settings.menu.label',
                'route' => 'ibexa_custom_settings_index',
                'extras' => ['translation_domain' => 'messages']
            ]);
        }
    }
}
