# Analytics by xdecaro

Analytics is the cross-product KPI, dataset, trend and reporting layer for the xdecaro Joomla ecosystem.

## 1.0.0

- provider API and event-based discovery;
- metrics and datasets without direct reads of private component tables;
- saved reports with provider-specific JSON context;
- metric snapshots for trends;
- stored report runs and CSV export;
- Joomla Scheduled Tasks refresh and retention maintenance;
- optional Core 1.4 `CapabilityRegistry` integration;
- shared Core UI assets when available;
- Joomla 4, 5 and 6 package distribution.

Analytics owns only analytics configuration, snapshots and cached report results. The originating product remains the source of truth for operational data.

### Provider contract

Integrations implement `AnalyticsProviderInterface` and register through `onXdecaroAnalyticsRegisterProviders`. Provider keys and metric/dataset keys are stable identifiers. Providers are responsible for their own ACL-sensitive query semantics; Analytics never treats an entity reference or a filter value as authorization.

### Capabilities

- `analytics.metrics`
- `analytics.datasets`
- `analytics.reports`
