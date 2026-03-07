# X Log Cleaner — Bulk Log Deletion for IPS4

An IPS4 plugin that adds bulk delete buttons to the System Logs and Error Logs pages in the AdminCP. Delete all logs at once or filter by category (system logs) or error level (error logs). File-based logs can also be purged. Every deletion is recorded in the ACP administrator audit log.

---

## Why?

IPS4's default log management requires deleting entries one by one or waiting for automatic pruning. When you need to clear thousands of log entries — after a migration, debugging session, or server incident — there's no built-in way to do it efficiently. X Log Cleaner adds sidebar buttons to both the System Logs and Error Logs pages for instant bulk cleanup.

## Features

### System Log Management
- **Bulk delete all** system logs from `core_log`
- **Selective deletion** by log category (dynamic multi-select)
- **File log purge** — delete all file-based logs from the fallback directory
- Read-only mode guard (`NO_WRITES`) for file operations

### Error Log Management
- **Bulk delete all** error logs from `core_error_logs`
- **Selective deletion** by error level (1-5, based on error code)

### Safety & Audit
- Confirmation checkbox required for every delete operation
- All deletions recorded in ACP administrator audit log
- Sidebar buttons disabled when no logs exist
- Modal dialog prevents accidental clicks

### Compatibility
- Works in both production and developer (`IN_DEV`) mode
- No database tables or settings — pure hook plugin
- IPS4 4.7+ / PHP 8.0+

## Installation

### 1. Download

Download the latest release from the [Releases](https://github.com/XENNTEC-UG/ips4-xlogcleaner/releases) page.

### 2. Install via ACP

1. Go to **AdminCP > System > Plugins**
2. Click **Install** and upload the plugin XML file
3. The bulk delete buttons will appear automatically on the System Logs and Error Logs pages

## File Structure

```
plugin-source/
  hooks/
    systemLogsController.php    System logs + file logs hook
    errorLogsController.php     Error logs hook
  dev/
    lang.php                    Language strings (prefix: xlc_)
    hooks.json                  Hook registration
    versions.json               Version history
```

## Documentation

| Document | Description |
|---|---|
| [FEATURES.MD](docs/FEATURES.MD) | Capability overview and current version |
| [ARCHITECTURE.md](docs/ARCHITECTURE.md) | Hook design, methods, data flow |
| [FLOW.md](docs/FLOW.md) | Entry points and runtime flow |
| [TEST_RUNTIME.md](docs/TEST_RUNTIME.md) | Manual verification procedures |
| [Releases](https://github.com/XENNTEC-UG/ips4-xlogcleaner/releases) | Version history and release notes |

## Contributing

Contributions are welcome. Please open an issue first to discuss what you'd like to change.

## License

This project is free to use. See the repository for license details.

## Links

- [IPS4 / Invision Community](https://invisioncommunity.com) — Community platform
- [XENNTEC](https://xenntec.com) — Developer

---

**Made by [XENNTEC](https://github.com/XENNTEC-UG)**
