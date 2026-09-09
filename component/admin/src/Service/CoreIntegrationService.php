<?php
namespace xdecaro\Component\Analytics\Administrator\Service;

defined('_JEXEC') or die;

use Joomla\CMS\WebAsset\WebAssetManager;
use xdecaro\Core\Asset\AssetService;
use xdecaro\Core\Integration\Capability;
use xdecaro\Core\Integration\CapabilityRegistry;
use xdecaro\Core\Integration\EntityReference;

final class CoreIntegrationService
{
    public function isAvailable(): bool { return class_exists(Capability::class) && class_exists(EntityReference::class); }
    public function hasCapabilityRegistry(): bool { return class_exists(CapabilityRegistry::class); }
    public function getCapabilities(): array
    {
        if (!$this->isAvailable()) { return []; }
        return [
            new Capability('com_xdecaroanalytics', 'analytics.metrics', '1'),
            new Capability('com_xdecaroanalytics', 'analytics.datasets', '1'),
            new Capability('com_xdecaroanalytics', 'analytics.reports', '1'),
        ];
    }
    public function registerCapabilities($registry): bool
    {
        if (!class_exists(CapabilityRegistry::class) || !$registry instanceof CapabilityRegistry) { return false; }
        $registry->registerMany($this->getCapabilities()); return true;
    }
    public function reportReference($id): ?EntityReference { return $this->isAvailable() ? new EntityReference('com_xdecaroanalytics', 'report', $id) : null; }
    public function useAssets(WebAssetManager $assets): bool
    {
        if (!class_exists(AssetService::class)) { return false; }
        return (new AssetService())->useComponents($assets);
    }
}
