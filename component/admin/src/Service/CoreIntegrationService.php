<?php
namespace xdecaro\Component\Analytics\Administrator\Service;

defined('_JEXEC') or die;

use xdecaro\Core\Integration\Capability;
use xdecaro\Core\Integration\EntityReference;

final class CoreIntegrationService
{
    public function isAvailable(): bool
    {
        return class_exists(Capability::class) && class_exists(EntityReference::class);
    }

    /** @return array<int,Capability> */
    public function getCapabilities(): array
    {
        if (!$this->isAvailable()) {
            return [];
        }

        return [
            new Capability('com_xdecaroanalytics', 'analytics.metrics', '1'),
            new Capability('com_xdecaroanalytics', 'analytics.datasets', '1'),
            new Capability('com_xdecaroanalytics', 'analytics.reports', '1'),
        ];
    }

    /** @param int|string $id */
    public function reportReference($id): ?EntityReference
    {
        return $this->isAvailable()
            ? new EntityReference('com_xdecaroanalytics', 'report', $id)
            : null;
    }
}
