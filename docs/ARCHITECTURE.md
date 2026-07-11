# Architecture: X Log Cleaner

## Hook Design

Two class hooks, one per ACP controller:

| Hook File | Target Class | Methods |
|---|---|---|
| `systemLogsController.php` | `\IPS\core\modules\admin\support\systemLogs` | `hookData()`, `manage()`, `fileLogs()`, `xlcDeleteSystemLogs()`, `xlcDeleteFileLogs()` |
| `errorLogsController.php` | `\IPS\core\modules\admin\support\errorLogs` | `hookData()`, `manage()`, `xlcDeleteErrorLogs()` |

## Method Reference

### systemLogsController.php

| Method | Type | Purpose |
|---|---|---|
| `hookData()` | Static hook metadata | Returns an empty array without a return type for IPS 4.7.24 hook compatibility. |
| `manage()` | Override | Injects "Delete System Logs" sidebar button. Disabled if `core_log` is empty. |
| `fileLogs()` | Override | Injects "Delete File Logs" sidebar button before parent renders. |
| `xlcDeleteSystemLogs()` | New | Form: YesNo toggle (all vs category), category multi-select, confirmation checkbox. Deletes from `core_log`. |
| `xlcDeleteFileLogs()` | New | Form: confirmation checkbox. Iterates `\IPS\Log::fallbackDir()` and attempts to unlink every entry except dot entries and `index.html`. Checks `NO_WRITES`. |

### errorLogsController.php

| Method | Type | Purpose |
|---|---|---|
| `hookData()` | Static hook metadata | Returns an empty array without a return type for IPS 4.7.24 hook compatibility. |
| `manage()` | Override | Injects "Delete Error Logs" sidebar button. Disabled if `core_error_logs` is empty. |
| `xlcDeleteErrorLogs()` | New | Form: YesNo toggle (all vs level), error level checkbox set, confirmation checkbox. Offers recognized levels found in the table, or levels 1 through 5 if level discovery throws an exception. Deletes from `core_error_logs`. |

## Database Tables

| Table | Used For |
|---|---|
| `core_log` | System logs, with category-based filtering |
| `core_error_logs` | Error logs, with level-based filtering by the first digit of `log_error_code` |

## Error Codes

| Code | Location | Meaning |
|---|---|---|
| `2XLC/1` | `xlcDeleteFileLogs()` | `NO_WRITES` is active |
| `2XLC/2` | `xlcDeleteFileLogs()` | File could not be deleted |

## Safety Mechanisms

- Confirmation checkbox required on all delete forms and validated server-side
- `NO_WRITES` guard on file deletion
- Successful deletion branches logged to the ACP administrator audit trail
- Database-log sidebar buttons disabled when their corresponding tables are empty
- Action links request modal rendering through `ipsDialog`

## Settings and Tasks

The plugin defines no settings or scheduled tasks. Its runtime behavior is entirely controller-driven through the two class hooks registered in `plugin-source/dev/hooks.json`.

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
- Database filters use IPS query builders or parameterized placeholders
- Language prefix: `xlc_`
