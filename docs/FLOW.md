# Flow: X Log Cleaner

## Entry Points

### System Logs Page

```mermaid
flowchart TD
    A[ACP System Logs controller] --> B[manage calls parent manage]
    B --> C[Count rows in core_log]
    C --> D{Rows exist?}
    D -->|Yes| E[Add linked Delete System Logs action]
    D -->|No| F[Add disabled Delete System Logs action]
    E --> G[ipsDialog opens xlcDeleteSystemLogs]
    G --> H[CSRF check and confirmation form]
    H --> I{Submitted choice}
    I -->|Delete all| J[Delete all core_log rows]
    I -->|Selected categories| K[Delete core_log rows in categories]
    I -->|Neither| L[No database deletion]
    J --> M[Write ACP audit entry]
    K --> M
    M --> N[Redirect to System Logs with deleted flash]
    L --> N
```

```mermaid
flowchart TD
    A[ACP System Logs File Logs action] --> B[fileLogs adds Delete File Logs action]
    B --> C[fileLogs calls parent fileLogs]
    B --> D[ipsDialog opens xlcDeleteFileLogs]
    D --> E[CSRF check]
    E --> F{NO_WRITES active?}
    F -->|Yes| G[Return 403 error 2XLC/1]
    F -->|No| H[Show confirmation form]
    H --> I{Confirmed submission?}
    I -->|No| J[Render form or redirect after unconfirmed submission]
    I -->|Yes| K[Read fallbackDir]
    K --> L{Directory exists?}
    L -->|Yes| M[Iterate entries except dot entries and index.html]
    M --> N{unlink succeeds?}
    N -->|No| O[Return 403 error 2XLC/2]
    N -->|Yes| P[Continue iteration]
    L -->|No| Q[Skip iteration]
    P --> R[Write ACP audit entry]
    Q --> R
    R --> S[Redirect to System Logs with deleted flash]
    J --> S
```

### Error Logs Page

```mermaid
flowchart TD
    A[ACP Error Logs controller] --> B[manage calls parent manage]
    B --> C[Count rows in core_error_logs]
    C --> D{Rows exist?}
    D -->|Yes| E[Add linked Delete Error Logs action]
    D -->|No| F[Add disabled Delete Error Logs action]
    E --> G[ipsDialog opens xlcDeleteErrorLogs]
    G --> H[CSRF check and confirmation form]
    H --> I[Discover distinct first digits of log_error_code]
    I --> J{Discovery succeeds?}
    J -->|Yes| K[Offer recognized levels found]
    J -->|No| L[Offer levels 1 through 5]
    K --> M{Submitted choice}
    L --> M
    M -->|Delete all| N[Delete all core_error_logs rows]
    M -->|Selected levels| O[Delete rows using parameterized LIKE conditions]
    M -->|Neither| P[No database deletion]
    N --> Q[Write ACP audit entry]
    O --> Q
    Q --> R[Redirect to Error Logs with deleted flash]
    P --> R
```

## Form Flow (all three delete dialogs)

```mermaid
flowchart TD
    A[Action URL receives request] --> B[Run CSRF check]
    B --> C[Build IPS form]
    C --> D[User chooses all or filters when available]
    D --> E[User checks confirmation]
    E --> F[Server validates confirmation]
    F --> G{Deletion choice present?}
    G -->|Yes| H[Execute deletion]
    H --> I[Write ACP audit entry]
    I --> J[Redirect with deleted flash]
    G -->|No| J
```

## Hook Exception Flow

Each hooked method catches `\Error` and `\RuntimeException`. If the parent class has a method with the same name, the hook calls that parent method with the original arguments. Otherwise, it rethrows the exception.
