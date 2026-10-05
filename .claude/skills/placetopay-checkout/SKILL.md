---
name: placetopay-checkout
description: Implement or extend PlacetoPay Web Checkout (Evertec) payments in SchoolSoft — adding a new thing parents can pay for (new payable type), enabling PlacetoPay for a new school/tenant, or reviewing a payment flow against the Evertec "INS-PR Checklist pago básico WC" certification. Use whenever the task touches PlacetoPayCheckout, PlacetoPayPaymentProcessor, placetopay_sessions, the PlacetoPay webhook/cron, or a parents payment form.
---

# PlacetoPay Web Checkout in SchoolSoft

The integration is already built and certified-ready. New payment flows **reuse it**; never call the `dnetix/redirection` SDK directly from a page.

## Architecture (read these first)

| Piece | File | Role |
|---|---|---|
| SDK wrapper | `app/Services/PlacetoPayCheckout.php` | `createSession()`, `querySession()`, `generateReference()`, `readNotification()` / `isValidNotification()`. Always sends `buyer` (name, surname, email, mobile) and `CustomerAccountNumber`. Expiration `EXPIRATION_MINUTES = 30`. |
| Processor | `app/Services/PlacetoPayPaymentProcessor.php` | **Single idempotent place** that applies a session result to its payable. `sync()` queries the API and, inside a transaction with `lockForUpdate()`, credits (`apply`), reverts refunds (`revert`) or discards rejected attempts (`discard`). Also `pendingFor($accountId)` (double-payment guard) and `validateBuyer($_POST)` / `normalizeMobile()`. |
| Model | `app/Models/PlacetoPaySession.php` | Table `placetopay_sessions`. Scopes `pending()`, `forAccount()`, `history()`. `applied_at` / `refunded_at` make crediting and refunding run once. |
| Status enum | `app/Enums/PlacetoPaySessionStatus.php` | `fromApiStatus()`, `isApproved()`, `isPending()`, `isRejected()`, `getLabel()`, `getBadgeClass()`. |
| Return pages | `demo/parents/options/{deposit,stores}/includes/return.php` | Find session by `reference` → `processor->sync()` → redirect to the summary page. Not gated by login. |
| Start pages | `demo/parents/options/{deposit,stores}/includes/start.php` | Validate → `pendingFor()` → persist payable → `createSession()` → redirect to `process_url`. |
| Parent pages | `demo/parents/options/placetopay/{result,history,terms}.php` | Summary after return, payment history, terms & conditions. |
| Webhook | `demo/webhooks/placetopay.php` | Validates signature, re-queries via `sync()`. Covers approvals, ACH and refunds/reversals. |
| Probe (cron) | `demo/cron/placetopay.php?token=` | Re-syncs pending sessions + approved ones from the last 30 days (catches lost refund notifications). |
| Translations | `lang/es/placetopay.php`, `lang/en/placetopay.php` | All flow strings, used as `__('placetopay.<group>.<key>')`. |
| Schema | `database/migrations/2026_10_02_placetopay_sessions_certification.sql` | Columns added for certification. |
| Config | `demo/config/services.php` → `placetopay.{login,tran_key,base_url,cron_token}` | Per tenant, read with `school_config('services.placetopay.*')`. |

Existing payable types (morph map in `bootstrap.php`): `'student'` (cafeteria deposit, `payable_id` = student `mt`) and `'store_order'` (`compras.id`).

## Adding a new payable (e.g. tuition, enrollment fee)

1. **Morph map**: add `'<alias>' => Model::class` in `bootstrap.php` `Relation::enforceMorphMap()`. Always store the alias in `payable_type`, never `Model::class`.
2. **Processor**: add the alias to the three `match ($session->payable_type)` blocks in `PlacetoPayPaymentProcessor`:
   - `apply()` → mark paid / credit. Must be safe to call when already applied (it is guarded by `applied_at`, but also guard on the payable's own state).
   - `revert()` → undo on refund (balance back, `paid = 0`, etc.).
   - `discard()` → only if an unpaid record was created *before* the checkout (like store orders) and should be removed when rejected/cancelled.
   Use `withoutGlobalScopes()` / `withoutGlobalScope(YearScope::class)` when finding the payable: webhook and cron run outside the current-year context.
3. **Form page** (copy `demo/parents/options/deposit/index.php` pattern):
   - Fields `first_name`, `last_name`, `email`, `mobile` (prefill from `Family::find(Session::id())->cel_m ?: cel_p`), plus required checkbox `terms` linking to `../placetopay/terms.php` (target `_blank`).
   - Show `__('placetopay.pending.warning')` when `(new PlacetoPayPaymentProcessor())->pendingFor(Session::id())` returns a session, with a link to `../placetopay/result.php?reference=`.
   - On valid submit, disable the button and show `__('placetopay.form.processing')` with a spinner; reload on `pageshow` with `event.persisted`.
   - Don't start the submit button `disabled` waiting for some other UI action — validate on submit and show the reason instead.
   - Link to `../placetopay/history.php`.
4. **start.php**:
   ```php
   Session::is_logged();
   if ($error = PlacetoPayPaymentProcessor::validateBuyer($_POST)) { Route::redirect($url . '...&message=' . urlencode($error)); }
   if ($pending = (new PlacetoPayPaymentProcessor())->pendingFor(Session::id())) { /* redirect with placetopay.errors.pending_blocked */ }
   // compute the amount SERVER SIDE, never trust the posted total
   $reference = PlacetoPayCheckout::generateReference('<PREFIX>', $id);   // unique, ≤ 32 chars
   $session = (new PlacetoPayCheckout())->createSession([
       'reference' => $reference, 'description' => '...', 'amount' => $amount,
       'accountId' => Session::id(),
       'buyerEmail' => ..., 'buyerName' => ..., 'buyerSurname' => ...,
       'buyerMobile' => PlacetoPayPaymentProcessor::normalizeMobile($_POST['mobile']),
       'payableType' => '<alias>', 'payableId' => (string) $id,
       'returnUrl' => school_url('parents/options/<flow>/includes/return.php?reference=' . $reference),
       'skipResult' => true,
   ], [], $items /* optional payment.items */);
   ```
   If you persist an unpaid record before the checkout and `createSession()` throws or returns no `process_url`, delete that record.
5. **return.php**: copy `deposit/includes/return.php`; only change the fallback redirect. Do **not** add `Session::is_logged()` — the buyer may return without a session.
6. **Result page back link**: add the alias to the `match` for `$backUrl` in `placetopay/result.php`.
7. **Translations**: add new strings to both `lang/es/placetopay.php` and `lang/en/placetopay.php`; never hardcode Spanish in JS — pass strings from PHP (`json_encode([...])` into a `const`).

## Enabling PlacetoPay for a new school (tenant)

1. Run `database/migrations/2026_10_02_placetopay_sessions_certification.sql` on the tenant DB (the base `placetopay_sessions` table must already exist).
2. Add `placetopay` credentials + a random `cron_token` (`bin2hex(random_bytes(20))`) to `<school>/config/services.php`. Test: `https://checkout-test.placetopay.com`.
3. Copy into the school folder: `webhooks/placetopay.php`, `cron/placetopay.php`, `parents/options/placetopay/`, and the flow folders.
4. In the PlacetoPay console set the notification URL `https://<domain>/<school>/webhooks/placetopay.php` (public port 443, < 128 chars, no URL shortener).
5. Hosting cron: `curl -s "https://<domain>/<school>/cron/placetopay.php?token=<cron_token>"` — every 5–10 min **only while testing**, once a day in production.

## Certification checklist mapping (INS-PR Pago Básico WC V2)

| Item | Where it's satisfied |
|---|---|
| Terms & conditions | `terms` checkbox + `placetopay/terms.php`; validated in `validateBuyer()` |
| IVU taxes | N/A (no taxed products). If ever needed, send `payment.amount.taxes` with base/kind/amount |
| Expiration 10–30 min | `PlacetoPayCheckout::EXPIRATION_MINUTES` |
| Button control (double request) | JS disables button + server `pendingFor()` |
| Cancel / status handling / retry | `return.php` always re-queries → `result.php` shows reference, amount + currency, status, date |
| Lightbox | N/A (plain redirect; `returnUrl` always sent) |
| Buyer fields + email format | `validateBuyer()` (`FILTER_VALIDATE_EMAIL`, 10–15 digit mobile) |
| Unique reference ≤ 32 | `generateReference()` loops on `referenceExists()` + UNIQUE index |
| Double payment message | `pendingFor()` warning on page + block in start.php |
| CustomerAccountNumber | always added in `createSession()` |
| Webhook / ACH webhook | `webhooks/placetopay.php` |
| Cron / probe | `cron/placetopay.php` |
| Transaction history | `placetopay/history.php` |
| Reversals | Done in PlacetoPay console → webhook/probe → `revert()` undoes automatically |

## Gotchas learned the hard way

- **Create-request status ≠ payment status.** `createSession()` returns `OK` for the *request*; the row must stay `PENDING`. Never treat `OK` from creation as approved.
- **`Route::redirect()` is relative to the portal folder** (`/parents`). Use `'/options/...'`, not `'/parents/options/...'` (that produces `/demo/parents/parents/...`). For return pages use `header('Location: ' . school_url('parents/...'))`.
- **HTML `pattern` is compiled with the regex `v` flag**: escape `(`, `)` and `-` inside character classes, e.g. `\+?[\d\s\(\)\-]{10,20}`.
- Webhook: trust the payload only to identify the session (`requestId` + matching `reference`); always re-query with `sync()`. Signature is `sha1(requestId . status . date . tranKey)`, or `sha256:` prefixed.
- Refunds can appear as request status `REFUNDED` or as a transaction with `refunded()`; `isRefunded()` checks both.
- A deposit refund may leave a negative balance — that's intended (recorded as a negative `depositos` row with reference `<ref>-R`).
- There's no test suite; verify with `php -l`, the local server (`php -S`), and `checkout-test` test cards (approved, rejected → retry with pending card, "No deseo continuar").
