<?php
namespace Xdecaro\Component\Decaroanalytics\Administrator\Service;

defined('_JEXEC') or die;

use Xdecaro\Core\Integration\Capability;
use Xdecaro\Core\Integration\EntityReference;

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
            new Capability('com_decaroanalytics', 'analytics.metrics', '1'),
            new Capability('com_decaroanalytics', 'analytics.datasets', '1'),
            new Capability('com_decaroanalytics', 'analytics.reports', '1'),
        ];
    }

    /** @param int|string $id */
    public function reportReference($id): ?EntityReference
    {
        return $this->isAvailable()
            ? new EntityReference('com_decaroanalytics', 'report', $id)
            : null;
    }
}
