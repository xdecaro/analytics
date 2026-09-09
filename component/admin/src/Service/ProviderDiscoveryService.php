<?php
namespace xdecaro\Component\Analytics\Administrator\Service;

defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\Plugin\PluginHelper;
use xdecaro\Component\Analytics\Administrator\Event\RegisterProvidersEvent;

final class ProviderDiscoveryService
{
    private $registry;
    private $discovered = false;

    public function __construct(ProviderRegistry $registry)
    {
        $this->registry = $registry;
    }

    public function discover(): ProviderRegistry
    {
        if ($this->discovered) {
            return $this->registry;
        }

        PluginHelper::importPlugin('xdecaroanalytics');
        Factory::getApplication()->getDispatcher()->dispatch(
            RegisterProvidersEvent::NAME,
            new RegisterProvidersEvent($this->registry)
        );
        $this->discovered = true;

        return $this->registry;
    }
}
