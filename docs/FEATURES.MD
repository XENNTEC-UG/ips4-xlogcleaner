# X Log Cleaner Features

> Bulk delete system logs, error logs, and file-based logs from the IPS admin panel. `xlogcleaner` provides filtered and all-record cleanup actions with confirmation forms and ACP audit logging.

## Current Version

| Field | Value |
|---|---|
| Version | 1.0.3 |
| Long Version | 10003 |
| Type | Plugin |
| Compatibility | IPS4 4.7+ / PHP 8.1+ |

## Feature Pillars

| Pillar | Summary | Benefit |
| --- | --- | --- |
| System log cleanup | Deletes all rows from `core_log` or rows in selected categories from a dynamically populated multi-select | Gives administrators an all-record or category-filtered cleanup action in the system log viewer |
| Error log cleanup | Deletes all rows from `core_error_logs` or rows whose `log_error_code` begins with a selected recognized level from 1 through 5 | Gives administrators an all-record or level-filtered cleanup action in the error log viewer |
| File-based log cleanup | Attempts to unlink entries in `\IPS\Log::fallbackDir()`, excluding dot entries and `index.html`, and refuses the action when `NO_WRITES` is active | Adds a cleanup action for disk-based fallback logs |
| Confirmation forms | Requires a validated confirmation checkbox for every cleanup form and opens actions in an `ipsDialog` modal | Adds an explicit confirmation step before a deletion branch runs |
| Audit trail | Records each successful deletion branch in the ACP administrator log | Identifies whether all logs or a selected filter was deleted |
| Context-aware UI | Disables database-log cleanup buttons when the corresponding table is empty | Shows whether the database cleanup actions are currently available |
| Hook-only design | Adds no database tables, settings, tasks, templates, or JavaScript | Keeps runtime behavior in two ACP controller class hooks |

## Ideal Use Cases

- Admin teams clearing accumulated log data during maintenance windows.
- Development environments needing quick log resets between test cycles.
- Communities that need category-based or error-level-based cleanup of accumulated logs.
