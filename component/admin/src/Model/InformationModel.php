<?php
namespace xdecaro\Component\Analytics\Administrator\Model;

defined('_JEXEC') or die;

use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\Factory;
use Joomla\CMS\MVC\Model\BaseDatabaseModel;
use Joomla\CMS\Plugin\PluginHelper;
use xdecaro\Component\Analytics\Administrator\Extension\AnalyticsComponent;

final class InformationModel extends BaseDatabaseModel
{
    public function getDiagnostics(): array
    {
        $db = $this->getDatabase(); $component = Factory::getApplication()->bootComponent('com_xdecaroanalytics');
        $core = $component instanceof AnalyticsComponent ? $component->getCoreIntegrationService() : null;
        $tables = [];
        foreach (['reports','snapshots','report_runs'] as $suffix) {
            $name = $db->replacePrefix('#__xdecaroanalytics_' . $suffix);
            $query = $db->getQuery(true)->select('COUNT(*)')->from('information_schema.tables')->where('table_schema = DATABASE()')->where('table_name = :table')->bind(':table', $name);
            try { $tables[$suffix] = (int) $db->setQuery($query)->loadResult() === 1; } catch (\Throwable $e) { $tables[$suffix] = null; }
        }
        return [
            'version' => '1.0.0', 'joomla' => JVERSION, 'php' => PHP_VERSION,
            'core_available' => $core ? $core->isAvailable() : false,
            'capability_registry' => $core ? $core->hasCapabilityRegistry() : false,
            'task_plugin' => PluginHelper::isEnabled('task', 'xdecaroanalytics'),
            'providers_plugin_group' => count(PluginHelper::getPlugin('xdecaroanalytics')),
            'tables' => $tables,
            'scheduled_batch' => (int) ComponentHelper::getParams('com_xdecaroanalytics')->get('scheduled_batch', 25),
        ];
    }
}
