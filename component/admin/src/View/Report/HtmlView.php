<?php
namespace xdecaro\Component\Analytics\Administrator\View\Report;

defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\View\HtmlView as BaseHtmlView;
use Joomla\CMS\Toolbar\ToolbarHelper;
use xdecaro\Component\Analytics\Administrator\Extension\AnalyticsComponent;

final class HtmlView extends BaseHtmlView
{
    public $item=[]; public $form; public $latestRun; public $providers=[];
    public function display($tpl=null): void
    {
        $this->item=$this->getModel()->getItem(); $this->form=$this->getModel()->getForm(); $this->latestRun=$this->getModel()->getLatestRun(); $this->providers=$this->getModel()->getProviders();
        ToolbarHelper::title($this->item['id']?Text::_('COM_XDECAROANALYTICS_EDIT_REPORT'):Text::_('COM_XDECAROANALYTICS_NEW_REPORT'),'chart');
        if(Factory::getApplication()->getIdentity()->authorise('analytics.manage','com_xdecaroanalytics')){ToolbarHelper::apply('report.save');}
        ToolbarHelper::cancel('report.cancel');
        $component=Factory::getApplication()->bootComponent('com_xdecaroanalytics'); if($component instanceof AnalyticsComponent){$component->getCoreIntegrationService()->useAssets(Factory::getApplication()->getDocument()->getWebAssetManager());}
        parent::display($tpl);
    }
}
