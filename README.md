# Analytics by xdecaro

Analytics provides cross-product dashboards, KPI, metrics, datasets, trends and reports for the xdecaro Joomla ecosystem.

It does not become the operational source of truth. Source data stays owned by the originating components; Analytics reads documented providers/events and may maintain only analytics-owned caches or snapshots where justified.

Initial Core integration targets:

- `Xdecaro\Core\Integration\EntityReference`
- `Xdecaro\Core\Integration\Capability`
- `Xdecaro\Core\Integration\IntegrationEvent`
- shared Core UI assets when available

Initial capabilities:

- `analytics.metrics`
- `analytics.datasets`
- `analytics.reports`

Target Joomla 4, 5 and 6 where technically possible.
