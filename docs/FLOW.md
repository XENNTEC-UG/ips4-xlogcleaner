# Flow — X Log Cleaner

## Entry Points

### System Logs Page
```
ACP → System → Support → System Logs
  → manage() hook injects sidebar button "Delete System Logs"
  → Click → modal dialog → xlcDeleteSystemLogs()
    → Delete all: Db::delete('core_log') → audit log → redirect
    → By category: Db::delete('core_log', IN categories) → audit log → redirect

ACP → System → Support → System Logs → File Logs
  → fileLogs() hook injects sidebar button "Delete File Logs"
  → Click → modal dialog → xlcDeleteFileLogs()
    → NO_WRITES check
    → DirectoryIterator → unlink each file → audit log → redirect
```

### Error Logs Page
```
ACP → System → Support → Error Logs
  → manage() hook injects sidebar button "Delete Error Logs"
  → Click → modal dialog → xlcDeleteErrorLogs()
    → Delete all: Db::delete('core_error_logs') → audit log → redirect
    → By level: Db::delete('core_error_logs', LIKE 'N%') → audit log → redirect
```

## Form Flow (all three delete dialogs)

```
1. Modal opens with form
2. User picks "delete all" (YesNo toggle) or selects filter options
3. User checks confirmation checkbox
4. Submit → server validates confirmation
5. Execute deletion → log to ACP audit → redirect with "deleted" flash message
```
