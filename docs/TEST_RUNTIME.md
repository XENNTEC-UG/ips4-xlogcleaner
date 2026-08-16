# Test Runtime — X Log Cleaner

## Prerequisites

- Plugin installed and enabled in ACP → System → Plugins
- Import-sync run: `ips-dev-sync.ps1 -Mode import`
- Hook active (check ACP → System → Plugins → X Log Cleaner → hooks enabled)

## Test Cases

### TC1: System Logs — Button Visibility

1. Navigate to ACP → System → Support → System Logs
2. **If logs exist**: sidebar shows enabled "Delete System Logs" button with trash icon
3. **If no logs**: sidebar shows disabled (greyed) "Delete System Logs" button

### TC2: System Logs — Delete All

1. Click "Delete System Logs" button
2. Modal dialog opens with form
3. Toggle "Delete all?" to Yes — category select hides
4. Check "Confirm Delete" checkbox
5. Submit → redirects to system logs page with success message
6. Verify `core_log` table is empty

### TC3: System Logs — Delete by Category

1. Click "Delete System Logs" button
2. Leave "Delete all?" as No
3. Select one or more categories from the multi-select
4. Check "Confirm Delete" checkbox
5. Submit → only selected categories deleted
6. Verify remaining categories still have entries

### TC4: System Logs — Confirmation Required

1. Click "Delete System Logs" button
2. Submit WITHOUT checking confirmation
3. Form shows validation error "You must check the confirmation box..."

### TC5: File Logs — Delete All

1. Navigate to System Logs → File Logs (sidebar link)
2. "Delete File Logs" button appears in sidebar
3. Click → modal dialog → check confirmation → submit
4. Verify files in `\IPS\Log::fallbackDir()` are deleted (except `.` files and `index.html`)

### TC6: Error Logs — Button Visibility

1. Navigate to ACP → System → Support → Error Logs
2. **If errors exist**: sidebar shows enabled "Delete Error Logs" button
3. **If no errors**: sidebar shows disabled button

### TC7: Error Logs — Delete All

1. Click "Delete Error Logs" button
2. Toggle "Delete all?" to Yes
3. Check confirmation → submit
4. Verify `core_error_logs` table is empty

### TC8: Error Logs — Delete by Level

1. Click "Delete Error Logs" button
2. Leave "Delete all?" as No
3. Select specific error levels (e.g. Level 2, Level 4)
4. Check confirmation → submit
5. Verify only entries with matching `log_error_code` first digit are deleted

### TC9: ACP Audit Trail

After each deletion action, check ACP → System → Logs → Administrator Logs for:
- "Deleted all system logs"
- "Deleted system logs in categories: ..."
- "Deleted all system file logs"
- "Deleted all error logs"
- "Deleted error logs at levels: ..."

### TC10: Error Logs — ACP Restriction

1. Sign in with a staff account that has only the core `system_logs_view` ACP restriction
2. Navigate to ACP → System → Support → Error Logs and verify there is no "Delete Error Logs" button
3. Open the direct `do=xlcDeleteErrorLogs` URL and verify the response is HTTP 403
4. Grant the core `diagnostic_log_settings` ACP restriction (`core` / `support`)
5. Verify the "Delete Error Logs" button is restored and the direct action URL opens the delete form
