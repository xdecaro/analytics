<?php
namespace xdecaro\Component\Analytics\Administrator\Extension;

defined('_JEXEC') or die;

use Joomla\CMS\Extension\MVCComponent;
use LogicException;
use xdecaro\Component\Analytics\Administrator\Contract\AnalyticsProviderInterface;
use xdecaro\Component\Analytics\Administrator\Service\AnalyticsService;
use xdecaro\Component\Analytics\Administrator\Service\CoreIntegrationService;
use xdecaro\Component\Analytics\Administrator\Service\MaintenanceService;
use xdecaro\Component\Analytics\Administrator\Service\ProviderDiscoveryService;
use xdecaro\Component\Analytics\Administrator\Service\ProviderRegistry;
use xdecaro\Component\Analytics\Administrator\Service\ReportService;
use xdecaro\Component\Analytics\Administrator\Service\SnapshotService;

final class AnalyticsComponent extends MVCComponent
{
    private $analyticsService; private $reportService; private $snapshotService; private $providerRegistry; private $providerDiscoveryService; private $maintenanceService; private $coreIntegrationService;
    public function setAnalyticsService(AnalyticsService $service): void{$this->analyticsService=$service;} public function setReportService(ReportService $service): void{$this->reportService=$service;} public function setSnapshotService(SnapshotService $service): void{$this->snapshotService=$service;} public function setProviderRegistry(ProviderRegistry $registry): void{$this->providerRegistry=$registry;} public function setProviderDiscoveryService(ProviderDiscoveryService $service): void{$this->providerDiscoveryService=$service;} public function setMaintenanceService(MaintenanceService $service): void{$this->maintenanceService=$service;} public function setCoreIntegrationService(CoreIntegrationService $service): void{$this->coreIntegrationService=$service;}
    public function getAnalyticsService(): AnalyticsService{if(!$this->analyticsService){throw new LogicException('Analytics service is not initialized.');}return $this->analyticsService;} public function getReportService(): ReportService{if(!$this->reportService){throw new LogicException('Report service is not initialized.');}return $this->reportService;} public function getSnapshotService(): SnapshotService{if(!$this->snapshotService){throw new LogicException('Snapshot service is not initialized.');}return $this->snapshotService;} public function getProviderRegistry(): ProviderRegistry{if(!$this->providerRegistry){throw new LogicException('Provider registry is not initialized.');}return $this->providerRegistry;} public function getProviderDiscoveryService(): ProviderDiscoveryService{if(!$this->providerDiscoveryService){throw new LogicException('Provider discovery service is not initialized.');}return $this->providerDiscoveryService;} public function getMaintenanceService(): MaintenanceService{if(!$this->maintenanceService){throw new LogicException('Maintenance service is not initialized.');}return $this->maintenanceService;} public function getCoreIntegrationService(): CoreIntegrationService{if(!$this->coreIntegrationService){throw new LogicException('Core integration service is not initialized.');}return $this->coreIntegrationService;}
    public function registerProvider(AnalyticsProviderInterface $provider): void{$this->getProviderRegistry()->register($provider);}
}
