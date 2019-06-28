<?php

namespace EzPlatformLogsUi\Bundle;

use EzPlatformLogsUi\Bundle\Security\LogsUiProvider;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\ExtensionInterface;
use Symfony\Component\HttpKernel\Bundle\Bundle;

/**
 * EzPlatformLogsUiBundle
 * Symfony bundle dedicated to eZ Platform, to add a log management interface to the back office.
 *
 * @author Florian Bouché <contact@florian-bouche.fr>
 * @license MIT
 *
 * @package EzPlatformLogsUi\Bundle
 */
class EzPlatformLogsUiBundle extends Bundle {

    /**
     * @param ContainerBuilder $container
     */
    public function build(ContainerBuilder $container): void {
        parent::build($container);

        /** @var ExtensionInterface $eZExtension */
        $eZExtension = $container->getExtension('ezpublish');
        $eZExtension->addPolicyProvider(new LogsUiProvider);
    }

}
