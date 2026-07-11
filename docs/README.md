# X Log Cleaner

ACP utility plugin that extends the IPS4 **System Logs** and **Error Logs** controllers with bulk cleanup actions. Administrators can delete every database log in a viewer, limit system-log deletion by category, or limit error-log deletion by the first digit of the error code. A separate action removes entries from the IPS fallback log directory. Each deletion path requires confirmation and writes an entry to the ACP administrator audit log.

The plugin uses class hooks and existing IPS forms, database access, session logging, and output handling. It adds no settings, tasks, templates, JavaScript, or database schema.

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
