<?php
namespace xdecaro\Component\Analytics\Administrator\Model;

defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\Form\Form;
use Joomla\CMS\MVC\Model\BaseDatabaseModel;
use xdecaro\Component\Analytics\Administrator\Extension\AnalyticsComponent;

final class ReportModel extends BaseDatabaseModel
{
    public function getItem(): array
    {
        $id=Factory::getApplication()->input->getInt('id');
        if($id<1){return ['id'=>0,'title'=>'','provider_key'=>'','source_type'=>'metric','source_key'=>'','context_json'=>'','scheduled'=>0,'state'=>1];}
        $item=$this->component()->getReportService()->get($id); $item['context_json']=$item['context']===[]?'':json_encode($item['context'],JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES); return $item;
    }
    public function getForm(): Form { Form::addFormPath(JPATH_COMPONENT_ADMINISTRATOR.'/forms'); $form=Form::getInstance('com_xdecaroanalytics.report','report',['control'=>'jform']); $form->bind($this->getItem()); return $form; }
    public function getLatestRun(): ?array { $id=Factory::getApplication()->input->getInt('id'); return $id>0?$this->component()->getReportService()->latestRun($id):null; }
    public function getProviders(): array { return $this->component()->getProviderDiscoveryService()->discover()->all(); }
    private function component(): AnalyticsComponent { $component=Factory::getApplication()->bootComponent('com_xdecaroanalytics'); if(!$component instanceof AnalyticsComponent){throw new \RuntimeException('Analytics component unavailable.');} return $component; }
}
