# AquaTrack security demonstration material

These scripts are isolated classroom demonstrations. They do not bootstrap Laravel, send HTTP requests, connect to AquaTrack's configured database, or change the production Water Source controller, model, observer, routes, or logs.

## 1. SQL Injection — OWASP A03:2021: Injection

This standalone script demonstrates only the vulnerable, **without secure coding practice** case. It builds SQL by concatenating user input into the query: `... WHERE id = ` + input.

With input `1 OR 1=1`, the final SQL becomes `WHERE id = 1 OR 1=1`. Because `1=1` is always true, the query returns every demonstration row.

The vulnerable example is intentionally marked `// UNSAFE - FOR DEMO ONLY, NEVER USE`. It uses only an in-memory SQLite table containing three demonstration rows; it has no connection to the AquaTrack database.

The script also prints explanatory, non-executable examples of other risks created by SQL injection: data exfiltration, destructive actions, authentication bypass, and privilege escalation. It does not perform any of those actions.

For the secure comparison, show the live AquaTrack Water Source module instead. Its controller, [`../app/Http/Controllers/WaterSourceController.php`](../app/Http/Controllers/WaterSourceController.php), uses Eloquent ORM operations such as `WaterSource::create()`, `->update()`, and `->delete()`, which use parameterized queries. The live website is therefore the safe half of the comparison.

Run live:

```powershell
php .\security-demo\sql_injection_demo.php
```

Point out the concatenated vulnerable query and its row count: `WHERE id = 1 OR 1=1` returns all three demonstration rows. Then show the equivalent safe CRUD operation live in the AquaTrack website.

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

## 3. Authentication & Password Management — Credential Theft via Data Breach

`password_hashing_demo.php` contrasts an unsafe plaintext password record with PHP's `password_hash()` and `password_verify()`. It uses only a fictional account in memory.

```powershell
php .\security-demo\password_hashing_demo.php
```

The secure AquaTrack implementation is in [`../app/Models/User.php`](../app/Models/User.php): its `password` cast is `hashed`.

## 4. Session Management — Session Hijacking

`session_regeneration_demo.php` shows why retaining a known pre-login session ID after authentication is unsafe, then demonstrates `session_regenerate_id(true)`. It uses an isolated temporary PHP session directory, not AquaTrack's session files.

```powershell
php .\security-demo\session_regeneration_demo.php
```

The secure AquaTrack implementation is in [`../app/Http/Controllers/AuthController.php`](../app/Http/Controllers/AuthController.php), which calls `$request->session()->regenerate()` after successful login.

## 5. Output Encoding — OWASP A03:2021: Injection (Cross-Site Scripting)

`output_encoding_demo.php` contrasts rendering a fictional malicious input, `<script>alert('XSS')</script>`, directly into HTML versus encoding it first with `htmlspecialchars()`. It uses only a hardcoded string in memory; it does not bootstrap Laravel, render a real Blade view, or touch AquaTrack's database.

```powershell
php .\security-demo\output_encoding_demo.php
```

The vulnerable section shows the `<script>` tag emitted unchanged inside a `<div>`, which a browser would execute as markup. The secure section shows the same input passed through `htmlspecialchars($input, ENT_QUOTES | ENT_SUBSTITUTE | ENT_HTML401, 'UTF-8')`, which converts `<`, `>`, `"`, `'`, and `&` into HTML entities so the browser renders it as inert text instead of executable script.

The secure AquaTrack implementation is in [`../resources/views/quality_readings/`](../resources/views/quality_readings/) (`index.blade.php`, `show.blade.php`, `create.blade.php`, `edit.blade.php`): every dynamic value is output via Blade's `{{ }}` syntax, which compiles to `htmlspecialchars()` automatically.

## 6. Access Control (Rate Limiting) — Automated Abuse / Application-Layer DoS

`rate_limiting_demo.php` simulates 10 requests to a fictional `GET /api/quality-readings` call using a simple in-memory request counter — it does not bootstrap Laravel, use the real `RateLimiter`, send HTTP requests, or touch AquaTrack's routes.

```powershell
php .\security-demo\rate_limiting_demo.php
```

The vulnerable section shows all 10 simulated requests accepted because no limit is applied, which would let an automated client keep consuming application resources indefinitely. The secure section applies a small demonstration limit of 5 requests per minute (chosen only so the terminal output stays short enough to present): the first 5 requests are `ALLOWED`, and requests 6–10 are `BLOCKED (429)`. HTTP 429 means "Too Many Requests" — the server refuses further requests from the client until the limit window resets.

The secure AquaTrack implementation is in [`../routes/api.php`](../routes/api.php): the Quality Reading API routes are wrapped in `Route::middleware('throttle:60,1')`, which limits real traffic to 60 requests per minute. This demo uses 5/minute purely for a shorter classroom demonstration; production uses 60/minute.
