<?php
namespace xdecaro\Component\Analytics\Administrator\View\Dashboard;

defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\View\HtmlView as BaseHtmlView;
use Joomla\CMS\Toolbar\ToolbarHelper;
use xdecaro\Component\Analytics\Administrator\Extension\AnalyticsComponent;

final class HtmlView extends BaseHtmlView
{
    public $stats=[]; public $providers=[]; public $reports=[];
    public function display($tpl=null): void
    {
        ToolbarHelper::title(Text::_('COM_XDECAROANALYTICS_DASHBOARD'),'chart');
        if(Factory::getApplication()->getIdentity()->authorise('core.admin','com_xdecaroanalytics')){ToolbarHelper::preferences('com_xdecaroanalytics');}
        $component=Factory::getApplication()->bootComponent('com_xdecaroanalytics');
        if($component instanceof AnalyticsComponent){$component->getCoreIntegrationService()->useAssets(Factory::getApplication()->getDocument()->getWebAssetManager());}
        $this->stats=$this->getModel()->getStats(); $this->providers=$this->getModel()->getProviders(); $this->reports=$this->getModel()->getReports();
        parent::display($tpl);
    }
}
