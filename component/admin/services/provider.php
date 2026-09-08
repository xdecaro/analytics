<?php
namespace Xdecaro\Component\Decaroanalytics\Administrator\Service;

defined('_JEXEC') or die;

use Joomla\CMS\Dispatcher\ComponentDispatcherFactoryInterface;
use Joomla\CMS\Extension\ComponentInterface;
use Joomla\CMS\Extension\Service\Provider\ComponentDispatcherFactory;
use Joomla\CMS\Extension\Service\Provider\MVCFactory;
use Joomla\CMS\MVC\Factory\MVCFactoryInterface;
use Joomla\DI\Container;
use Joomla\DI\ServiceProviderInterface;
use Xdecaro\Component\Decaroanalytics\Administrator\Extension\DecaroanalyticsComponent;

return new class implements ServiceProviderInterface {
    public function register(Container $container): void
    {
        $container->registerServiceProvider(new MVCFactory('Xdecaro\\Component\\Decaroanalytics'));
        $container->registerServiceProvider(new ComponentDispatcherFactory('Xdecaro\\Component\\Decaroanalytics'));
        $container->share(CoreIntegrationService::class, static fn (): CoreIntegrationService => new CoreIntegrationService());
        $container->set(ComponentInterface::class, static fn (Container $container): ComponentInterface => new DecaroanalyticsComponent(
            $container->get(ComponentDispatcherFactoryInterface::class),
            $container->get(MVCFactoryInterface::class)
        ));
    }
};
