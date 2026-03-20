# Features — X Log Cleaner

## Current Version

| Field | Value |
|---|---|
| Version | 1.0.0 |
| Long Version | 10000 |
| Type | Plugin |
| Compatibility | IPS4 4.7+ / PHP 8.1+ |

## Capabilities

### System Log Management
- Bulk delete all system logs from `core_log`
- Selective deletion by log category (dynamic multi-select)
- Bulk delete all file-based logs from the fallback directory
- Read-only mode guard (`NO_WRITES`) for file operations

### Error Log Management
- Bulk delete all error logs from `core_error_logs`
- Selective deletion by error level (1–5, based on `log_error_code` first digit)

### Safety & Audit
- Confirmation checkbox required for every delete operation
- All deletions recorded in ACP administrator log
- Sidebar buttons disabled when no logs exist
- Modal dialog prevents accidental clicks

### Compatibility
- Works in both live production and developer (IN_DEV) mode
- No database tables or settings — pure hook plugin
