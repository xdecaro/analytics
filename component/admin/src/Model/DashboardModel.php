<?php
namespace xdecaro\Component\Analytics\Administrator\Model;

defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\MVC\Model\BaseDatabaseModel;
use xdecaro\Component\Analytics\Administrator\Extension\AnalyticsComponent;

final class DashboardModel extends BaseDatabaseModel
{
    public function getStats(): array
    {
        $component = $this->component(); $providers = $component->getProviderDiscoveryService()->discover()->all();
        $metricCount = 0; $datasetCount = 0;
        foreach ($providers as $provider) { $metricCount += count($provider->getMetrics()); $datasetCount += count($provider->getDatasets()); }
        return ['providers'=>count($providers),'metrics'=>$metricCount,'datasets'=>$datasetCount,'reports'=>$this->countTable('#__xdecaroanalytics_reports'),'snapshots'=>$this->countTable('#__xdecaroanalytics_snapshots'),'runs'=>$this->countTable('#__xdecaroanalytics_report_runs')];
    }
    public function getProviders(): array { return $this->component()->getProviderDiscoveryService()->discover()->all(); }
    public function getReports(): array { return $this->component()->getReportService()->all(20); }
    private function countTable(string $table): int { $db=$this->getDatabase(); return (int)$db->setQuery($db->getQuery(true)->select('COUNT(*)')->from($db->quoteName($table)))->loadResult(); }
    private function component(): AnalyticsComponent { $component=Factory::getApplication()->bootComponent('com_xdecaroanalytics'); if(!$component instanceof AnalyticsComponent){throw new \RuntimeException('Analytics component unavailable.');} return $component; }
}
