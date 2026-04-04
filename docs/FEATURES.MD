# X Log Cleaner Features

> Bulk delete system logs, error logs, and file-based logs from the IPS admin panel. `xlogcleaner` provides selective and bulk log cleanup with safety confirmations and audit logging.

## Current Version

| Field | Value |
|---|---|
| Version | 1.0.1 |
| Long Version | 10001 |
| Type | Plugin |
| Compatibility | IPS4 4.7+ / PHP 8.1+ |

## Feature Pillars

| Pillar | Summary | Benefit |
| --- | --- | --- |
| System log cleanup | Bulk delete all system logs from `core_log` or selectively by category via dynamic multi-select | Quickly reclaim database space and reduce noise in the admin log viewer |
| Error log cleanup | Bulk delete all error logs from `core_error_logs` or selectively by error level (1-5) | Target specific severity tiers without losing lower-priority diagnostic data |
| File-based log cleanup | Bulk delete all file-based logs from the fallback directory with read-only mode guard (`NO_WRITES`) | Clears disk-resident logs safely, respecting environment write restrictions |
| Safety confirmations | Confirmation checkbox required for every delete operation with modal dialog to prevent accidental clicks | Eliminates risk of unintended bulk data loss |
| Audit trail | All deletions recorded in the ACP administrator log | Maintains accountability and change history for compliance |
| Context-aware UI | Sidebar buttons disabled when no logs exist for a given category | Prevents confusion by only exposing actionable operations |
| Zero-footprint design | No database tables or settings — pure hook plugin compatible with live and developer (IN_DEV) mode | Installs and removes cleanly with no schema changes or residual data |

## Ideal Use Cases

- Admin teams clearing accumulated log data during maintenance windows.
- Development environments needing quick log resets between test cycles.
- Communities with high traffic generating excessive system and error logs.
