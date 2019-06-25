<?php

namespace EzPlatformLogsUi\Bundle\EventListener;

use EzSystems\EzPlatformAdminUi\Menu\Event\ConfigureMenuEvent;
use EzSystems\EzPlatformAdminUi\Menu\MainMenuBuilder;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * Class AdminMenuListener
 *
 * @author Florian Bouché <contact@florian.bouche.fr>
 *
 * @package EzPlatformLogsUi\Bundle\EventListener
 */
class AdminMenuListener implements EventSubscriberInterface {

    /**
     * @return array
     */
    public static function getSubscribedEvents(): array {
        return [
            ConfigureMenuEvent::MAIN_MENU => ['onMenuConfigure', 0]
        ];
    }

    /**
     * @param ConfigureMenuEvent $event
     */
    public function onMenuConfigure(ConfigureMenuEvent $event): void {
        $menu = $event->getMenu();
        $menu[MainMenuBuilder::ITEM_ADMIN]->addChild('Logs', ['route' => 'ezplatform_logs_ui_index']);
    }

}
