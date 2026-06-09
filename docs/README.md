# X Log Cleaner

ACP utility plugin that adds bulk delete buttons to the **System Logs** and **Error Logs** pages. Administrators can delete all logs at once or filter by category (system logs) or error level (error logs). File-based logs can also be purged. Every deletion is recorded in the ACP administrator audit log.

Replaces the third-party "(TB) Delete All System Logs Button" plugin with XENNTEC-standard naming, expanded scope (error logs), and aligned code quality.

## Read Order

1. [GitHub Issues](https://github.com/XENNTEC-UG/ips4-xlogcleaner/issues): open bugs, enhancements, ideas
2. [ARCHITECTURE.md](ARCHITECTURE.md): hook design, methods, data flow
3. [FEATURES.MD](FEATURES.MD): capability overview and current version
4. [FLOW.md](FLOW.md): entry points and runtime flow
5. [TEST_RUNTIME.md](TEST_RUNTIME.md): manual verification procedures

## Source Paths

| File | Purpose |
|---|---|
| `plugin-source/hooks/systemLogsController.php` | System logs + file logs hook |
| `plugin-source/hooks/errorLogsController.php` | Error logs hook |
| `plugin-source/dev/lang.php` | Language strings (prefix: `xlc_`) |
| `plugin-source/dev/hooks.json` | Hook registration |
| `plugin-source/dev/versions.json` | Version registry |

## Source of Truth

- **Source**: `ips-dev-source/plugins/xlogcleaner/plugin-source/`
- **Runtime**: `data/ips/plugins/xlogcleaner/` (synced via `ips-dev-sync.ps1 -Mode import`)

## Global Context

- [Root README](../../../README.md): stack setup, Docker, SSL
- [IPS4 Dev Guide](../../../IPS4_DEV_GUIDE.md): coding standards, sync workflow
- [AI Tools](../../../AI_TOOLS.md): MCP tool reference, browser testing
- [CLAUDE.md](../../../CLAUDE.md): project routing hub, component registry
