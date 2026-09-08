# Analytics — Repository Guidelines

## Scope

Analytics by xdecaro provides cross-product dashboards, KPI, metrics, datasets, trends and reports. It owns metric definitions, report configuration and analytics-specific caches or snapshots when justified.

Operational source data remains owned by the originating components. Analytics must not become the source of truth for competitions, finance, membership, courses, events, bookings, documents or other domains.

## Core integration

Use Xdecaro Core only through documented public APIs. Prefer `EntityReference`, `Capability`, `IntegrationEvent` and shared UI assets when available. Core integration must remain optional and degrade safely when Core is missing or too old.

Initial public capabilities are `analytics.metrics`, `analytics.datasets` and `analytics.reports`.

Prefer documented providers and events over direct reads of private component tables. Never treat an entity reference as proof of authorization.

## Joomla

Target Joomla 4, 5 and 6 where technically possible. Use namespaces, MVC, service providers, DI, ACL, CSRF protection, filtered input, escaped output, Language API and Web Asset Manager. Use `#__` for future tables and preserve data on updates.

## UI

Use Core shared UI assets when available. Keep responsive, accessible, light/dark compatible dashboards and reports. Charts must retain accessible textual equivalents and must not communicate state only by color.

## Working rule

When the user says `procedi`, execute directly after inspecting current code and dependencies. Preserve working behavior and avoid unnecessary refactors.
