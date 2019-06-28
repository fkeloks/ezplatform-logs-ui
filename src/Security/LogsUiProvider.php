<?php

namespace EzPlatformLogsUi\Bundle\Security;

use eZ\Bundle\EzPublishCoreBundle\DependencyInjection\Security\PolicyProvider\YamlPolicyProvider;

/**
 * Class LogsUiProvider
 *
 * @author Florian Bouché <contact@florian.bouche.fr>
 *
 * @package EzPlatformLogsUi\Bundle\Security
 */
class LogsUiProvider extends YamlPolicyProvider {

    /**
     * YAML based policy provider.
     *
     * @return array
     */
    protected function getFiles(): array {
        return [
            __DIR__ . '/../Resources/config/policies.yml'
        ];
    }

}
