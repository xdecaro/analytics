<?php
namespace xdecaro\Plugin\Task\Analytics\Extension;

defined('_JEXEC') or die;

use Joomla\CMS\Component\ComponentHelper; use Joomla\CMS\Factory; use Joomla\CMS\Plugin\CMSPlugin; use Joomla\Component\Scheduler\Administrator\Event\ExecuteTaskEvent; use Joomla\Component\Scheduler\Administrator\Task\Status; use Joomla\Component\Scheduler\Administrator\Traits\TaskPluginTrait; use Joomla\Event\SubscriberInterface; use Throwable; use xdecaro\Component\Analytics\Administrator\Extension\AnalyticsComponent;

final class Analytics extends CMSPlugin implements SubscriberInterface
{
    use TaskPluginTrait;
    protected const TASKS_MAP=['xdecaroanalytics.refresh'=>['langConstPrefix'=>'PLG_TASK_XDECAROANALYTICS_REFRESH','form'=>'refresh','method'=>'refresh'],'xdecaroanalytics.maintenance'=>['langConstPrefix'=>'PLG_TASK_XDECAROANALYTICS_MAINTENANCE','form'=>'maintenance','method'=>'maintenance']];
    protected $autoloadLanguage=true;
    public static function getSubscribedEvents(): array{return ['onTaskOptionsList'=>'advertiseRoutines','onExecuteTask'=>'standardRoutineHandler','onContentPrepareForm'=>'enhanceTaskItemForm'];}
    protected function refresh(ExecuteTaskEvent $event): int
    {
        try{$component=$this->bootComponent();$global=ComponentHelper::getParams('com_xdecaroanalytics');$params=$event->getArgument('params');$batch=max(1,min(100,(int)($params->batch??$global->get('scheduled_batch',25))));$stats=$component->getReportService()->runScheduled($batch);$this->logTask(sprintf('Analytics refresh: processed=%d success=%d failed=%d',$stats['processed'],$stats['success'],$stats['failed']));return Status::OK;}catch(Throwable $e){$this->logTask('Analytics refresh error: '.$e->getMessage(),'error');return Status::KNOCKOUT;}
    }
    protected function maintenance(ExecuteTaskEvent $event): int
    {
        try{$component=$this->bootComponent();$global=ComponentHelper::getParams('com_xdecaroanalytics');$params=$event->getArgument('params');$days=max(30,min(3650,(int)($params->retention_days??$global->get('snapshot_retention_days',730))));$stats=$component->getMaintenanceService()->run($days);$this->logTask(sprintf('Analytics maintenance: snapshots=%d runs=%d',$stats['snapshots'],$stats['runs']));return Status::OK;}catch(Throwable $e){$this->logTask('Analytics maintenance error: '.$e->getMessage(),'error');return Status::KNOCKOUT;}
    }
    private function bootComponent(): AnalyticsComponent{$component=Factory::getApplication()->bootComponent('com_xdecaroanalytics');if(!$component instanceof AnalyticsComponent){throw new \RuntimeException('Analytics component unavailable.');}return $component;}
}
