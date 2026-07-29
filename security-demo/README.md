# AquaTrack security demonstration material

These scripts are isolated classroom demonstrations. They do not bootstrap Laravel, send HTTP requests, connect to AquaTrack's configured database, or change the production Water Source controller, model, observer, routes, or logs.

## 1. SQL Injection — OWASP A03:2021: Injection

| Without secure coding | With secure coding |
| --- | --- |
| The demo builds SQL by concatenating the user input into the query: `... WHERE id = ` + input. | The demo uses a prepared statement: `WHERE id = :id`, then binds the input as `:id`. |
| With input `1 OR 1=1`, the final SQL becomes `WHERE id = 1 OR 1=1` and returns every demonstration row. | The same text is bound as a literal parameter, so it does not become part of the SQL grammar and returns no matching row. |

The vulnerable example is intentionally marked `// UNSAFE - FOR DEMO ONLY, NEVER USE`. It uses only an in-memory SQLite table containing three demonstration rows; it has no connection to the AquaTrack database. The secure half illustrates the parameterized-query principle used by Laravel Eloquent/query builder in the Water Source module.

Run live:

```powershell
php .\security-demo\sql_injection_demo.php
```

Point out the two displayed queries and row counts: the vulnerable query returns three rows; the prepared version treats `1 OR 1=1` as data.

## 2. Insider Threat / Repudiation — OWASP A09:2021: Security Logging and Monitoring Failures

Repudiation is the inability to reliably trace and attribute an action afterward. In OWASP Top 10 terms, missing or inadequate security logging/monitoring is covered by A09:2021.

| Without logging | With logging |
| --- | --- |
| An Administrator changes the demonstration water source, but no audit record is written. | The same type of update is written with actor ID, name, role, IP address, action, water-source ID, and before/after data. |
| Afterward, the example only shows the changed value; it cannot identify who performed the action. | The generated record identifies the demonstrated actor and the changed field. |

The production observer is [`../app/Observers/WaterSourceObserver.php`](../app/Observers/WaterSourceObserver.php), registered in [`../app/Providers/AppServiceProvider.php`](../app/Providers/AppServiceProvider.php). It uses Laravel's `admin_actions` channel, configured in [`../config/logging.php`](../config/logging.php) to write to `storage/logs/admin-actions.log`.

For isolation, the demonstration script does **not** invoke the production observer or production channel. It mirrors the observer's log message and context fields, and writes only this demo file:

```text
security-demo/output/admin-actions-demo.log
```

Example output format:

```text
[2026-07-29 12:00:00] local.INFO: water_source.updated {"action":"updated","water_source_id":7,"actor_id":42,"actor_name":"Demo Administrator","actor_role":"Administrator","ip_address":"203.0.113.42","before":{"location":"Kedah"},"after":{"location":"Perlis"}}
```

Run live:

```powershell
php .\security-demo\insider_repudiation_demo.php
Get-Content .\security-demo\output\admin-actions-demo.log
```

First show the no-logging section: the action occurs but leaves no attribution record. Then show the generated record and explain that its fields mirror the production `WaterSourceObserver` context. The record supports accountability for the action by recording who acted, what they changed, and the request IP; this demo does not claim that a local plaintext log is cryptographically immutable.

## Run both demonstrations

```powershell
php .\security-demo\sql_injection_demo.php
php .\security-demo\insider_repudiation_demo.php
```
