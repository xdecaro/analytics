<?php
namespace xdecaro\Component\Analytics\Administrator\View\Reports;

defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\View\HtmlView as BaseHtmlView;
use Joomla\CMS\Toolbar\ToolbarHelper;
use xdecaro\Component\Analytics\Administrator\Extension\AnalyticsComponent;

final class HtmlView extends BaseHtmlView
{
    public $items=[]; public $pagination; public $state;
    public function display($tpl=null): void
    {
        ToolbarHelper::title(Text::_('COM_XDECAROANALYTICS_REPORTS'),'chart');
        if(Factory::getApplication()->getIdentity()->authorise('analytics.manage','com_xdecaroanalytics')){ToolbarHelper::addNew('report.add');}
        $component=Factory::getApplication()->bootComponent('com_xdecaroanalytics'); if($component instanceof AnalyticsComponent){$component->getCoreIntegrationService()->useAssets(Factory::getApplication()->getDocument()->getWebAssetManager());}
        $this->items=$this->getModel()->getItems(); $this->pagination=$this->getModel()->getPagination(); $this->state=$this->getModel()->getState();
        parent::display($tpl);
    }
}
