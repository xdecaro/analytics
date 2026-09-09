<?php
namespace xdecaro\Component\Analytics\Administrator\Controller;

defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\Form\Form;
use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\Controller\BaseController;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Session\Session;
use Throwable;
use xdecaro\Component\Analytics\Administrator\Extension\AnalyticsComponent;

final class ReportController extends BaseController
{
    public function add(): void
    {
        if (!Factory::getApplication()->getIdentity()->authorise('analytics.manage','com_xdecaroanalytics')) { throw new \RuntimeException(Text::_('JERROR_ALERTNOAUTHOR'),403); }
        $this->setRedirect(Route::_('index.php?option=com_xdecaroanalytics&view=report',false));
    }
    public function save(): void
    {
        Session::checkToken() or jexit(Text::_('JINVALID_TOKEN'));
        $app=Factory::getApplication(); $user=$app->getIdentity();
        if(!$user->authorise('analytics.manage','com_xdecaroanalytics')){throw new \RuntimeException(Text::_('JERROR_ALERTNOAUTHOR'),403);}
        $data=(array)$app->input->post->get('jform',[],'array'); $id=(int)($data['id']??0);
        try{
            Form::addFormPath(JPATH_COMPONENT_ADMINISTRATOR.'/forms'); $form=Form::getInstance('com_xdecaroanalytics.report','report',['control'=>'jform']); $filtered=$form->filter($data);
            if($form->validate($filtered)===false){throw new \InvalidArgumentException(Text::_('COM_XDECAROANALYTICS_ERROR_VALIDATION'));}
            $filtered['context']=$filtered['context_json']??''; $id=$this->component()->getReportService()->save($filtered,$id); $app->enqueueMessage(Text::_('COM_XDECAROANALYTICS_REPORT_SAVED'),'success');
        }catch(Throwable $e){$app->enqueueMessage($e->getMessage(),'error');}
        $this->setRedirect(Route::_('index.php?option=com_xdecaroanalytics&view=report&id='.$id,false));
    }
    public function run(): void
    {
        Session::checkToken() or jexit(Text::_('JINVALID_TOKEN')); $app=Factory::getApplication(); $user=$app->getIdentity(); $id=$app->input->getInt('id');
        if(!$user->authorise('analytics.run','com_xdecaroanalytics')){throw new \RuntimeException(Text::_('JERROR_ALERTNOAUTHOR'),403);}
        try{$this->component()->getReportService()->run($id,(int)$user->id);$app->enqueueMessage(Text::_('COM_XDECAROANALYTICS_REPORT_RUN_OK'),'success');}catch(Throwable $e){$app->enqueueMessage($e->getMessage(),'error');}
        $this->setRedirect(Route::_('index.php?option=com_xdecaroanalytics&view=report&id='.$id,false));
    }
    public function export(): void
    {
        Session::checkToken() or jexit(Text::_('JINVALID_TOKEN')); $app=Factory::getApplication(); $user=$app->getIdentity(); $id=$app->input->getInt('id');
        if(!$user->authorise('analytics.export','com_xdecaroanalytics')){throw new \RuntimeException(Text::_('JERROR_ALERTNOAUTHOR'),403);}
        $csv=$this->component()->getReportService()->exportCsv($id); $app->setHeader('Content-Type','text/csv; charset=utf-8',true); $app->setHeader('Content-Disposition','attachment; filename="analytics-report-'.$id.'.csv"',true); echo $csv; $app->close();
    }
    public function cancel(): void{$this->setRedirect(Route::_('index.php?option=com_xdecaroanalytics&view=reports',false));}
    private function component(): AnalyticsComponent{$component=Factory::getApplication()->bootComponent('com_xdecaroanalytics');if(!$component instanceof AnalyticsComponent){throw new \RuntimeException('Analytics component unavailable.');}return $component;}
}
