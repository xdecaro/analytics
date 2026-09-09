<?php
namespace xdecaro\Component\Analytics\Administrator\View\Information;

defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\View\HtmlView as BaseHtmlView;
use Joomla\CMS\Toolbar\ToolbarHelper;
use xdecaro\Component\Analytics\Administrator\Extension\AnalyticsComponent;

final class HtmlView extends BaseHtmlView
{
    public $diagnostics=[];
    public function display($tpl=null): void
    {
        ToolbarHelper::title(Text::_('COM_XDECAROANALYTICS_INFORMATION'),'info-circle');
        $component=Factory::getApplication()->bootComponent('com_xdecaroanalytics'); if($component instanceof AnalyticsComponent){$component->getCoreIntegrationService()->useAssets(Factory::getApplication()->getDocument()->getWebAssetManager());}
        $this->diagnostics=$this->getModel()->getDiagnostics(); parent::display($tpl);
    }
}
