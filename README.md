# Analytics by xdecaro

Analytics provides cross-product dashboards, KPI, metrics, datasets, trends and reports for the xdecaro Joomla ecosystem.

## Technical identity

- Component: `com_xdecaroanalytics`
- PHP namespace: `Xdecaro\Component\Analytics`
- Reserved package identity: `pkg_xdecaroanalytics`
- Reserved database namespace: `#__xdecaroanalytics_*`

The package and database identifiers are reserved for future implementation; they must not be treated as shipped until their manifests/schema actually exist.

Analytics does not become the operational source of truth. Source data stays owned by the originating components; Analytics reads documented providers/events and may maintain only analytics-owned caches or snapshots where justified.

Initial Core integration targets:

- `Xdecaro\Core\Integration\EntityReference`
- `Xdecaro\Core\Integration\Capability`
- `Xdecaro\Core\Integration\IntegrationEvent`
- shared Core UI assets when available

Initial capabilities:

- `analytics.metrics`
- `analytics.datasets`
- `analytics.reports`

Target Joomla 4, 5 and 6 only where runtime compatibility is actually verified.
