# Architecture — X Log Cleaner

## Hook Design

Two class hooks, one per ACP controller:

| Hook File | Target Class | Methods |
|---|---|---|
| `systemLogsController.php` | `\IPS\core\modules\admin\support\systemLogs` | `manage()`, `fileLogs()`, `xlcDeleteSystemLogs()`, `xlcDeleteFileLogs()` |
| `errorLogsController.php` | `\IPS\core\modules\admin\support\errorLogs` | `manage()`, `xlcDeleteErrorLogs()` |

## Method Reference

### systemLogsController.php

| Method | Type | Purpose |
|---|---|---|
| `manage()` | Override | Injects "Delete System Logs" sidebar button. Disabled if `core_log` is empty. |
| `fileLogs()` | Override | Injects "Delete File Logs" sidebar button before parent renders. |
| `xlcDeleteSystemLogs()` | New | Form: YesNo toggle (all vs category), category multi-select, confirmation checkbox. Deletes from `core_log`. |
| `xlcDeleteFileLogs()` | New | Form: confirmation checkbox. Iterates `\IPS\Log::fallbackDir()`, deletes non-system files. Checks `NO_WRITES`. |

### errorLogsController.php

| Method | Type | Purpose |
|---|---|---|
| `manage()` | Override | Injects "Delete Error Logs" sidebar button. Disabled if `core_error_logs` is empty. |
| `xlcDeleteErrorLogs()` | New | Form: YesNo toggle (all vs level), error level checkbox set (levels 1-5), confirmation checkbox. Deletes from `core_error_logs`. |

## Database Tables

| Table | Used For |
|---|---|
| `core_log` | System logs — category-based filtering |
| `core_error_logs` | Error logs — level-based filtering (first digit of `log_error_code`) |

## Error Codes

| Code | Location | Meaning |
|---|---|---|
| `2XLC/1` | `xlcDeleteFileLogs()` | `NO_WRITES` is active |
| `2XLC/2` | `xlcDeleteFileLogs()` | File could not be deleted |

## Safety Mechanisms

- Confirmation checkbox required on all delete forms (validated server-side)
- `NO_WRITES` guard on file deletion
- All deletions logged to ACP administrator audit trail
- Sidebar button disabled when no logs exist
- Modal dialog (`ipsDialog`) prevents accidental clicks

## Hook Class IDs

| Hook File | Class Name |
|---|---|
| `systemLogsController.php` | `hook193` |
| `errorLogsController.php` | `hook476` |

## Code Patterns

All methods follow the xbulkdevtools reference:

- `try { ... } catch ( \Error | \RuntimeException $e )` with parent fallback
- CSRF check on action methods via `\IPS\Session::i()->csrfCheck()`
- Form validators throw `\DomainException`
- Language prefix: `xlc_`
