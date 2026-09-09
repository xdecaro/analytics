<?php
defined('_JEXEC') or die;
use Joomla\CMS\Factory; use Joomla\Database\DatabaseInterface; use Joomla\Database\ParameterType;
final class pkg_xdecaroanalyticsInstallerScript
{
    public function postflight($type,$parent): void
    {
        if(!in_array((string)$type,['install','discover_install'],true)){return;}
        $db=Factory::getContainer()->get(DatabaseInterface::class);$enabled=1;$plugin='plugin';$folder='task';$element='xdecaroanalytics';
        $query=$db->getQuery(true)->update($db->quoteName('#__extensions'))->set($db->quoteName('enabled').' = :enabled')->where($db->quoteName('type').' = :type')->where($db->quoteName('folder').' = :folder')->where($db->quoteName('element').' = :element')->bind(':enabled',$enabled,ParameterType::INTEGER)->bind(':type',$plugin)->bind(':folder',$folder)->bind(':element',$element);$db->setQuery($query)->execute();
    }
}
