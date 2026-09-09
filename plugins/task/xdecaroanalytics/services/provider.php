<?php
defined('_JEXEC') or die;
use Joomla\CMS\Extension\PluginInterface; use Joomla\CMS\Plugin\PluginHelper; use Joomla\DI\Container; use Joomla\DI\ServiceProviderInterface; use xdecaro\Plugin\Task\Analytics\Extension\Analytics;
return new class implements ServiceProviderInterface{public function register(Container $container): void{$container->set(PluginInterface::class,$container->lazy(Analytics::class,static function():Analytics{return new Analytics((array)PluginHelper::getPlugin('task','xdecaroanalytics'));}));}};
