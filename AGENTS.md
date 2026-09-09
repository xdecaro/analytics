# Analytics — Repository Guidelines

## Scope

Analytics by xdecaro provides cross-product dashboards, KPI, metrics, datasets, trends and reports. It owns report configuration, analytics-specific snapshots and cached report results only when justified.

Operational source data remains owned by the originating components. Analytics must never become the source of truth for competitions, finance, membership, courses, events, bookings, documents or other domains, and must never read another product's private tables directly.

## Provider boundary

All source data enters through documented `AnalyticsProviderInterface` implementations registered through `onXdecaroAnalyticsRegisterProviders`. Provider keys and metric/dataset keys are stable contracts. Do not accept arbitrary SQL, table names or executable query fragments from report configuration.

## Core integration

Use the canonical `xdecaro\Core` namespace and documented public APIs only. Prefer `EntityReference`, `Capability`, `CapabilityRegistry`, `IntegrationEvent` and shared UI assets when available. Core integration must remain optional and degrade safely when Core is missing or too old.

Public capabilities are `analytics.metrics`, `analytics.datasets` and `analytics.reports`.

## Joomla and security

Target Joomla 4, 5 and 6 where technically possible. Use namespaces, MVC, service providers, DI, ACL, CSRF protection, filtered input, escaped output, Language API and Web Asset Manager. Always use `#__`. Preserve data and administrator configuration on updates.

Reports may persist provider context only as validated JSON with bounded size. Dataset results are bounded before persistence/export. Exports require ACL and CSRF. Scheduler routines use public Analytics services rather than direct SQL against source products.

## UI

Use Core shared UI assets when available. Keep responsive, accessible and dark-mode compatible dashboards and reports. Charts must retain accessible textual equivalents and must not communicate state only by color.

## Release

Use Semantic Versioning. Keep component, package, Scheduler plugin, update feed, changelog, ZIP names and SHA-256 aligned. CI must validate PHP 7.4/8.3, deterministic builds and clean package installation on Joomla 4.4, 5.4 and 6.1.

## Working rule

When the user says `procedi`, execute directly after inspecting current code and dependencies. Preserve working behavior and avoid unnecessary refactors.
