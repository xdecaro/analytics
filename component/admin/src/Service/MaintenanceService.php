<?php
namespace xdecaro\Component\Analytics\Administrator\Service;

defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\Database\DatabaseInterface;

final class MaintenanceService
{
    private $db;
    public function __construct(DatabaseInterface $db){$this->db=$db;}
    public function run(int $retentionDays=730): array
    {
        $retentionDays=max(30,min(3650,$retentionDays)); $cutoff=Factory::getDate('-'.$retentionDays.' days')->toSql(); $stats=['snapshots'=>0,'runs'=>0];
        $query=$this->db->getQuery(true)->delete($this->db->quoteName('#__xdecaroanalytics_snapshots'))->where($this->db->quoteName('created').' < :cutoff')->bind(':cutoff',$cutoff); $this->db->setQuery($query)->execute(); $stats['snapshots']=$this->db->getAffectedRows();
        $query=$this->db->getQuery(true)->delete($this->db->quoteName('#__xdecaroanalytics_report_runs'))->where($this->db->quoteName('started_at').' < :cutoff')->bind(':cutoff',$cutoff); $this->db->setQuery($query)->execute(); $stats['runs']=$this->db->getAffectedRows();
        return $stats;
    }
}
