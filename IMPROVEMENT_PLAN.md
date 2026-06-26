# CCS Calculator — Complete Improvement & Modernization Plan

**Plugin:** The Child Care Subsidy Calculator
**Current version:** 2.1.0 · **Target version:** 3.0.0
**Author:** i9 Education
**Plan date:** 2026-06-26
**Owner:** _(assign)_

---

## 0. Backups (DONE — restore points before any work)

Three independent restore points were created on 2026-06-26 **before** any code changes:

| Type | Location / Ref | Restore command |
|---|---|---|
| ZIP archive | `~/childcare-backups/childcare-subsidy-calculator-backup-20260626.zip` (2.8 MB, integrity-verified) | `unzip -o <zip> -d <target>` |
| Git tag | `backup-pre-overhaul-20260626` | `git checkout backup-pre-overhaul-20260626` |
| Git branch | `backup/pre-overhaul-20260626` | `git checkout backup/pre-overhaul-20260626` |

> **Rule:** Each phase below is committed on its own feature branch and merged only after testing. If anything breaks, revert the branch or restore from the points above.

---

## 1. Executive Summary

The calculator is a feature-rich lead-generation tool with solid admin-side security and real CCS domain logic, but it has **three classes of problems**:

1. **Critical front-end security holes** — an open, nonce-less, unauthenticated AJAX endpoint that can send email to anyone and flood the database; raw HTML injected into emails (stored XSS).
2. **Untrustworthy calculations** — all math runs client-side and can be tampered with; care-type/age cap logic is wrong; duplicated, hardcoded rate tables bypass admin settings.
3. **Severe maintainability & performance debt** — a 7,021-line admin file, ~120 autoloaded options, per-request CSS regeneration, an unbatched 18k-row import, and dead/debug files.

This plan fixes all of them in **6 phases** and then layers on **UX and "advanced" feature** upgrades to make the tool more effective and modern.

**Guiding priorities:** Security → Correctness → Maintainability → UX → Advanced features.

---

## 2. Phase Plan Overview

| Phase | Theme | Risk if skipped | Effort | Priority |
|---|---|---|---|---|
| **P0** | Security hardening (front-end AJAX) | Spam relay, DoS, stored XSS | M | 🔴 Must |
| **P1** | Calculation correctness & trust | Wrong/forgeable estimates | M–L | 🔴 Must |
| **P2** | Code cleanup & dead-file removal | Confusion, attack surface | S | 🟠 High |
| **P3** | Maintainability refactor (admin) | Slow future changes | L | 🟠 High |
| **P4** | Performance | Slow site-wide page loads | M | 🟡 Med |
| **P5** | Privacy & compliance | Legal risk (Privacy Act/GDPR) | M | 🟡 Med |
| **P6** | UX & advanced features | Competitiveness | L | 🟢 Growth |

Effort: S = ≤1 day, M = 2–4 days, L = 1–2 weeks.

---

## 3. Phase 0 — Security Hardening 🔴 (do first, ship as 2.1.1 hotfix)

**Goal:** Close the unauthenticated abuse surface before anything else.

### 3.1 Add nonce verification to all front-end AJAX
- Files: `includes/Ajax/Email.php`, `includes/Ajax/SuburbSearch.php`, `includes/Frontend/Shortcode.php`.
- Emit a nonce via `wp_localize_script()` (e.g. `ccs_ajax.nonce = wp_create_nonce('ccs_frontend')`).
- In each handler add `check_ajax_referer('ccs_frontend', 'nonce')` as the first line.
- Update the JS `$.post`/`$.ajax` calls to include `nonce: ccs_ajax.nonce`.

### 3.2 Stop the open email relay
- Validate recipient with `is_email()` before `wp_mail()` ([Email.php:30,259]).
- Add **rate limiting**: transient-based per-IP cap (e.g. 5 sends / 10 min) **and** a CAPTCHA (Cloudflare Turnstile or reCAPTCHA v3) gating `send_summary_email`.
- Add a honeypot field to the form as a cheap bot filter.

### 3.3 Fix stored XSS / HTML injection
- Stop concatenating `summary_html` raw into emails and `post_content` ([Email.php:49,197,390]).
- Preferred: rebuild the summary **server-side** from individual sanitized fields (see P1.3).
- Interim: pass `summary_html` through a strict `wp_kses()` allowlist (only `table/tr/td/th/strong/span/br` + a fixed style attr whitelist).

### 3.4 Remove the debug file from production
- Delete `check-email-settings.php` (bootstraps `wp-load.php`, reads plugin source). Keep a copy in a dev-only `/tools` folder excluded from the release zip.

**Acceptance criteria (P0):**
- [ ] Direct `curl` POST to `admin-ajax.php?action=send_summary_email` without a valid nonce returns 403.
- [ ] Submitting `<script>`/`<img onerror>` in any field never executes in email or wp-admin.
- [ ] More than N rapid submissions from one IP are throttled.
- [ ] `check-email-settings.php` is not present in the release build.

---

## 4. Phase 1 — Calculation Correctness & Trust 🔴

**Goal:** Make the numbers correct, consistent, and tamper-proof.

### 4.1 Move (or mirror) the calculation server-side
- Create `includes/Calculator/CCSEngine.php` — a single authoritative PHP class implementing the full CCS calc.
- JS may still compute for **instant preview**, but the **emailed/stored** result is recomputed server-side from raw inputs (income, children, ages, hours, fees) so it cannot be forged.
- The AJAX payload should send **raw inputs**, not pre-rendered HTML.

### 4.2 Fix the hourly-cap logic
- Cap must depend on **selected care type** (Centre-Based Day Care, OSHC, Family Day Care, In-Home Care), not just `age < 6`.
- Add a care-type selector per child in the form; expose Family Day Care ($13.56) and In-Home Care ($39.80) which currently can't be selected.

### 4.3 Unify the rate logic & kill magic numbers
- Merge the **two inconsistent "Higher CCS"** functions (`calculateHigherCCS` vs `calculateHigherCCSFromATI`) into one tested function with one set of breakpoints.
- Align age thresholds (`age <= 5` vs `age < 6`) into one definition.
- Move **all** hardcoded constants (14.63, 12.81, 13.56, 39.80, 85279, 535279, 367563, 5000, taper divisors) into the `childcare_ccs_policy` option so a rate change is a settings edit, not a code edit.
- Replace the fragile `getAge()` epoch trick with a proper date-diff.

### 4.4 Add bounds validation to policy settings
- In `childcare_ccs_policy_sanitize()` enforce ordering/ranges (e.g. `zero_threshold > base_threshold`, caps > 0, percentages 0–100). Reject + admin notice on invalid input.

### 4.5 Lock behaviour with tests
- Add a PHP test harness (`tests/`) with known CCS scenarios (low income 90%, taper, 33% floor, second-child higher rate, 3-Day Guarantee 72/100 hrs, withholding). Run on every change to the engine.

**Acceptance criteria (P1):**
- [ ] Identical inputs produce identical results in JS preview and server recompute.
- [ ] Care type changes the applied cap correctly.
- [ ] All rate constants are editable from Settings; no rate literal remains in JS.
- [ ] Test suite passes for the documented scenarios.

---

## 5. Phase 2 — Cleanup & Dead-Code Removal 🟠

- Delete unused `includes/Autoloader.php` **or** wire it up to replace the 9 manual `require_once` calls (pick one; recommend wiring it up via PSR-4).
- Delete `includes/Ajax/Email.backup.php` and `assets/css/childcare-backup.css`.
- Gate all `error_log()` / `console.log()` behind `WP_DEBUG`.
- Add a proper `.gitignore` (`.DS_Store`, `release/`, `node_modules/`, `vendor/`).
- Remove `.DS_Store` files from the repo and the release zip.

**Acceptance criteria:** repo has no `*backup*` source files, no stray debug logging in production, clean `git status`.

---

## 6. Phase 3 — Maintainability Refactor (Admin Layer) 🟠

**Goal:** Shrink the 7,021-line `Menu.php` and remove copy-paste.

- Introduce a **data-driven field config**: define every styling/policy/email field once in a config array (key, label, type, default, sanitizer). Loop over it to (a) register settings, (b) render form rows, (c) read values in Assets. This collapses `Menu.php`, `Settings.php`, and `Assets.php` dramatically.
- Move inline admin CSS/JS out of PHP into real `assets/admin/*.css` / `*.js` files, enqueued normally.
- Split `Menu.php` page-render methods into separate template/partial files (`includes/Admin/views/`).
- Same treatment for the giant inline front-end JS in `Shortcode.php` → move into the (currently empty) `assets/js/calculator.js`, enqueued + versioned.

**Acceptance criteria:** `Menu.php` < ~800 lines; no inline `<style>`/`<script>` blocks > 50 lines in PHP; adding a new styling option is a one-line config change.

---

## 7. Phase 4 — Performance 🟡

- **Consolidate ~120 autoloaded `ccs_*` options** into one (or a few) array options to stop bloating WordPress's site-wide alloptions cache.
- **Cache generated CSS to a file** written on settings-save (with a versioned filename), instead of regenerating from 157 `get_option()` calls on every page load. Enqueue the static file.
- **Batch the suburb import**: multi-row `INSERT` (e.g. 500/row) inside a transaction so a mid-import failure rolls back instead of leaving a truncated table. Stream the CSV instead of `explode("\n")` on the whole body.
- Lazy-load the suburbs autocomplete and `intl-tel-input` only on pages containing the shortcode.

**Acceptance criteria:** front-end page load issues a single enqueued CSS file; non-calculator pages don't load calculator options; full suburb import completes atomically.

---

## 8. Phase 5 — Privacy & Compliance 🟡

- Add an explicit **consent checkbox** ("I agree to be contacted / privacy policy") before submission; store consent + timestamp with the submission.
- Add **data retention**: setting for auto-purge of `ccs_submission` posts older than N days (WP-Cron).
- Register WordPress **privacy export & erase hooks** so submissions (incl. ATSI status) are covered by data-subject requests.
- Treat **ATSI status** as sensitive: store only if provided, document it in the privacy policy section of `readme.txt`.
- Prominent, unavoidable **"estimate only — verify with Services Australia"** disclaimer on results.

**Acceptance criteria:** WP privacy exporter/eraser returns submission data; old submissions auto-purge; consent recorded.

---

## 9. Phase 6 — UX & Advanced Features 🟢 (make it more effective & modern)

**Usability**
- Real-time inline validation + clearer step errors; disable "Next" until a step is valid.
- Progress autosave to `localStorage` so a refresh doesn't lose input.
- Accessibility pass: ARIA on steps, keyboard nav, focus management, WCAG AA contrast.
- Mobile polish: larger tap targets, sticky summary bar.

**Result quality**
- Downloadable **PDF** of the estimate (server-generated) in addition to email.
- Clear **fortnightly + weekly + annual** breakdown with a simple chart.
- "What changes my subsidy?" explainer tooltips tied to each input.

**Advanced**
- **Scenario comparison**: compare 2–3 income/hours scenarios side by side.
- **Shareable result link** (tokenized, read-only) instead of emailing HTML.
- **Provider presets**: admin can preconfigure typical daily fees/hours per their centre.
- **Multi-language** readiness: ensure all strings use `__()`/`esc_html__()` and ship a `.pot`.
- **Analytics events** (GA4/HubSpot) for funnel drop-off per step.
- Optional **REST API endpoint** (`/wp-json/ccs/v1/estimate`) for headless/embedded use, reusing the P1 engine.

**Acceptance criteria:** documented per feature; each ships behind a setting toggle where it changes default behaviour.

---

## 10. Suggested Timeline & Sequencing

```
Week 1      P0 Security hotfix  → release 2.1.1
Week 2–3    P1 Calculation engine + tests
Week 3      P2 Cleanup (parallel, low risk)
Week 4–5    P3 Admin refactor
Week 6      P4 Performance
Week 7      P5 Privacy/compliance  → release 2.2.0
Week 8+     P6 UX & advanced features → release 3.0.0
```

- **2.1.1** = P0 only (ship ASAP).
- **2.2.0** = P1–P5 (the "correct, clean, compliant" release).
- **3.0.0** = P6 (the "advanced" release).

---

## 11. Testing & Rollback Strategy

- **Branch per phase**; merge only after manual + automated tests pass on a staging site.
- Keep the three Section-0 backups untouched until 3.0.0 is stable in production.
- Smoke-test checklist before each release: activate plugin, run a full calculation (multi-child), confirm email arrives & is safe, confirm submission saved, confirm suburb search, confirm admin settings save.
- Test matrix: WP 5.0 + latest, PHP 7.4 + 8.1/8.3, mobile + desktop, with/without HubSpot.
- Update `readme.txt` `Tested up to` and the `CHANGELOG.md` each release.

---

## 12. Definition of Done (overall)

- [ ] No unauthenticated/abusable AJAX endpoints; no stored XSS.
- [ ] Server-side authoritative, fully configurable, tested calculations.
- [ ] `Menu.php` refactored; no dead/debug/backup files in release.
- [ ] Single cached CSS file; consolidated options; atomic suburb import.
- [ ] Privacy export/erase + consent + retention in place.
- [ ] Advanced UX features shipped behind toggles; docs + changelog updated.
- [ ] All three backups still available until 3.0.0 is proven stable.
