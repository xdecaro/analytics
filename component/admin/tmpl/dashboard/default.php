<?php
defined('_JEXEC') or die;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Router\Route;
?>
<div class="container-fluid xdecaro-component">
  <div class="row g-3 mb-4">
    <?php foreach(['providers','metrics','datasets','reports','snapshots','runs'] as $key): ?>
      <div class="col-6 col-md-4 col-xl-2"><div class="card h-100"><div class="card-body"><div class="text-muted small"><?php echo Text::_('COM_XDECAROANALYTICS_STAT_'.strtoupper($key)); ?></div><div class="fs-2 fw-semibold"><?php echo (int)($this->stats[$key]??0); ?></div></div></div></div>
    <?php endforeach; ?>
  </div>
  <div class="card mb-4"><div class="card-header d-flex justify-content-between align-items-center"><strong><?php echo Text::_('COM_XDECAROANALYTICS_PROVIDERS'); ?></strong><a class="btn btn-sm btn-outline-primary" href="<?php echo Route::_('index.php?option=com_xdecaroanalytics&view=reports'); ?>"><?php echo Text::_('COM_XDECAROANALYTICS_REPORTS'); ?></a></div><div class="card-body">
    <?php if(!$this->providers): ?><div class="alert alert-info mb-0"><?php echo Text::_('COM_XDECAROANALYTICS_NO_PROVIDERS'); ?></div><?php else: ?>
      <div class="row g-3"><?php foreach($this->providers as $key=>$provider): ?><div class="col-12 col-lg-6"><div class="border rounded p-3 h-100"><div class="fw-semibold"><?php echo htmlspecialchars($provider->getLabel(),ENT_QUOTES,'UTF-8'); ?></div><div class="text-muted small mb-2"><code><?php echo htmlspecialchars($key,ENT_QUOTES,'UTF-8'); ?></code></div><span class="badge bg-info"><?php echo count($provider->getMetrics()); ?> <?php echo Text::_('COM_XDECAROANALYTICS_METRICS'); ?></span> <span class="badge bg-secondary"><?php echo count($provider->getDatasets()); ?> <?php echo Text::_('COM_XDECAROANALYTICS_DATASETS'); ?></span></div></div><?php endforeach; ?></div>
    <?php endif; ?>
  </div></div>
  <div class="card"><div class="card-header"><strong><?php echo Text::_('COM_XDECAROANALYTICS_RECENT_REPORTS'); ?></strong></div><div class="table-responsive"><table class="table table-hover mb-0"><thead><tr><th><?php echo Text::_('COM_XDECAROANALYTICS_FIELD_TITLE'); ?></th><th><?php echo Text::_('COM_XDECAROANALYTICS_FIELD_PROVIDER'); ?></th><th><?php echo Text::_('COM_XDECAROANALYTICS_FIELD_SOURCE_TYPE'); ?></th></tr></thead><tbody><?php if(!$this->reports): ?><tr><td colspan="3" class="text-muted"><?php echo Text::_('COM_XDECAROANALYTICS_NO_REPORTS'); ?></td></tr><?php else: foreach($this->reports as $report): ?><tr><td><a href="<?php echo Route::_('index.php?option=com_xdecaroanalytics&view=report&id='.(int)$report['id']); ?>"><?php echo htmlspecialchars($report['title'],ENT_QUOTES,'UTF-8'); ?></a></td><td><code><?php echo htmlspecialchars($report['provider_key'],ENT_QUOTES,'UTF-8'); ?></code></td><td><?php echo htmlspecialchars($report['source_type'],ENT_QUOTES,'UTF-8'); ?></td></tr><?php endforeach; endif; ?></tbody></table></div></div>
</div>
