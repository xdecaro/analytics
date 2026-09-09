<?php
namespace xdecaro\Component\Analytics\Administrator\Model;

defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\MVC\Model\ListModel;

final class ReportsModel extends ListModel
{
    protected $filter_fields=['id','title','provider_key','source_type','source_key','scheduled','state'];
    protected function populateState($ordering='title',$direction='ASC'): void
    {
        $app=Factory::getApplication(); $this->setState('filter.search',$app->getUserStateFromRequest($this->context.'.filter.search','filter_search','','string')); $this->setState('filter.source_type',$app->getUserStateFromRequest($this->context.'.filter.source_type','filter_source_type','','cmd')); parent::populateState($ordering,$direction);
    }
    protected function getListQuery()
    {
        $db=$this->getDatabase(); $query=$db->getQuery(true)->select('r.*')->from($db->quoteName('#__xdecaroanalytics_reports','r')); $search=trim((string)$this->getState('filter.search'));
        if($search!==''){$token='%'.str_replace(['%','_'],['\\%','\\_'],$search).'%';$query->where('('.$db->quoteName('r.title').' LIKE :search OR '.$db->quoteName('r.provider_key').' LIKE :search)')->bind(':search',$token);}
        $type=(string)$this->getState('filter.source_type');if(in_array($type,['metric','dataset'],true)){$query->where($db->quoteName('r.source_type').' = :source_type')->bind(':source_type',$type);}
        $ordering=(string)$this->getState('list.ordering','title');if(!in_array($ordering,$this->filter_fields,true)){$ordering='title';}$direction=strtoupper((string)$this->getState('list.direction','ASC'))==='DESC'?'DESC':'ASC';$query->order($db->quoteName('r.'.$ordering).' '.$direction);return $query;
    }
}
