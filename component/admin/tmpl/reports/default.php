<?php
defined('_JEXEC') or die;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Router\Route;
?>
<form action="<?php echo Route::_('index.php?option=com_xdecaroanalytics&view=reports'); ?>" method="post" id="adminForm" name="adminForm">
<div class="container-fluid xdecaro-component">
  <div class="row g-2 mb-3"><div class="col-12 col-md-6"><input class="form-control" type="search" name="filter_search" value="<?php echo htmlspecialchars((string)$this->state->get('filter.search'),ENT_QUOTES,'UTF-8'); ?>" placeholder="<?php echo Text::_('JSEARCH_FILTER'); ?>"></div><div class="col-8 col-md-3"><select class="form-select" name="filter_source_type"><option value=""><?php echo Text::_('COM_XDECAROANALYTICS_ALL_TYPES'); ?></option><option value="metric"<?php echo $this->state->get('filter.source_type')==='metric'?' selected':''; ?>><?php echo Text::_('COM_XDECAROANALYTICS_SOURCE_METRIC'); ?></option><option value="dataset"<?php echo $this->state->get('filter.source_type')==='dataset'?' selected':''; ?>><?php echo Text::_('COM_XDECAROANALYTICS_SOURCE_DATASET'); ?></option></select></div><div class="col-4 col-md-3"><button class="btn btn-primary w-100" type="submit"><?php echo Text::_('JFILTER'); ?></button></div></div>
  <div class="card"><div class="table-responsive"><table class="table table-hover align-middle mb-0"><thead><tr><th><?php echo Text::_('COM_XDECAROANALYTICS_FIELD_TITLE'); ?></th><th><?php echo Text::_('COM_XDECAROANALYTICS_FIELD_PROVIDER'); ?></th><th><?php echo Text::_('COM_XDECAROANALYTICS_FIELD_SOURCE_TYPE'); ?></th><th><?php echo Text::_('COM_XDECAROANALYTICS_FIELD_SOURCE_KEY'); ?></th><th><?php echo Text::_('COM_XDECAROANALYTICS_FIELD_SCHEDULED'); ?></th><th><?php echo Text::_('JSTATUS'); ?></th></tr></thead><tbody>
  <?php if(!$this->items): ?><tr><td colspan="6" class="text-muted p-4"><?php echo Text::_('COM_XDECAROANALYTICS_NO_REPORTS'); ?></td></tr><?php else: foreach($this->items as $item): ?><tr><td><a href="<?php echo Route::_('index.php?option=com_xdecaroanalytics&view=report&id='.(int)$item->id); ?>"><?php echo htmlspecialchars($item->title,ENT_QUOTES,'UTF-8'); ?></a></td><td><code><?php echo htmlspecialchars($item->provider_key,ENT_QUOTES,'UTF-8'); ?></code></td><td><span class="badge bg-secondary"><?php echo htmlspecialchars($item->source_type,ENT_QUOTES,'UTF-8'); ?></span></td><td><code><?php echo htmlspecialchars($item->source_key,ENT_QUOTES,'UTF-8'); ?></code></td><td><?php echo (int)$item->scheduled===1?Text::_('JYES'):Text::_('JNO'); ?></td><td><?php echo (int)$item->state===1?Text::_('JPUBLISHED'):Text::_('JUNPUBLISHED'); ?></td></tr><?php endforeach; endif; ?>
  </tbody></table></div></div>
  <div class="mt-3"><?php echo $this->pagination->getListFooter(); ?></div>
</div>
<?php echo HTMLHelper::_('form.token'); ?>
</form>
