<?php
namespace xdecaro\Component\Analytics\Administrator\Service;

defined('_JEXEC') or die;

use Joomla\CMS\Dispatcher\ComponentDispatcherFactoryInterface;
use Joomla\CMS\Extension\ComponentInterface;
use Joomla\CMS\Extension\Service\Provider\ComponentDispatcherFactory;
use Joomla\CMS\Extension\Service\Provider\MVCFactory;
use Joomla\CMS\MVC\Factory\MVCFactoryInterface;
use Joomla\DI\Container;
use Joomla\DI\ServiceProviderInterface;
use xdecaro\Component\Analytics\Administrator\Extension\AnalyticsComponent;

return new class implements ServiceProviderInterface {
    public function register(Container $container): void
    {
        $container->registerServiceProvider(new MVCFactory('xdecaro\\Component\\Analytics'));
        $container->registerServiceProvider(new ComponentDispatcherFactory('xdecaro\\Component\\Analytics'));
        $container->share(CoreIntegrationService::class, static fn (): CoreIntegrationService => new CoreIntegrationService());
        $container->set(ComponentInterface::class, static fn (Container $container): ComponentInterface => new AnalyticsComponent(
            $container->get(ComponentDispatcherFactoryInterface::class),
            $container->get(MVCFactoryInterface::class)
        ));
    }
};
