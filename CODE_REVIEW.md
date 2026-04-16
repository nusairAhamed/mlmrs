# MLMRS Code Review Report

**Project:** Medical Laboratory Management and Reporting System (MLMRS)
**Framework:** Laravel 12 / PHP ^8.2
**Review Date:** 2026-03-15
**Branch Reviewed:** `dev`
**Reviewer:** Claude Code (Automated Static Analysis)

---

## Table of Contents

1. [Executive Summary](#1-executive-summary)
2. [Architecture Overview](#2-architecture-overview)
3. [Database Schema & Migrations](#3-database-schema--migrations)
4. [Security Vulnerabilities](#4-security-vulnerabilities)
5. [Code Quality](#5-code-quality)
6. [Database Query Efficiency](#6-database-query-efficiency)
7. [Feature Completeness](#7-feature-completeness)
8. [Validation & Error Handling](#8-validation--error-handling)
9. [Dependency Assessment](#9-dependency-assessment)
10. [Recommendations & Action Plan](#10-recommendations--action-plan)

---

## 1. Executive Summary

| Dimension | Rating | Notes |
|---|---|---|
| **Code Quality** | ⭐⭐⭐ (3/5) | Solid Laravel conventions; controllers are too fat |
| **Security** | ⭐⭐⭐ (3/5) | Auth in place; missing rate limiting & authorization policies |
| **DB Efficiency** | ⭐⭐⭐ (3/5) | Good indexes; some N+1 risks exist |
| **Feature Completeness** | ⭐⭐⭐⭐ (4/5) | Core system complete; SMS & report revision missing |
| **Test Coverage** | ⭐ (1/5) | No automated tests found |

**Overall:** The application is functionally complete and follows Laravel best practices at a surface level. The core workflow — patient registration → order creation → sample tracking → result entry → approval → report generation — is well-implemented. However, the codebase has meaningful security gaps (no rate limiting on public endpoints, no authorization policies), fat controllers that mix business logic with HTTP concerns, zero test coverage, and several data integrity risks. These issues are addressable and should be prioritized before production deployment.

---

## 2. Architecture Overview

### 2.1 System Components

```
┌─────────────────────────────────────────────────────────┐
│  Browser / Client                                        │
└──────────────┬──────────────────────────────────────────┘
               │ HTTPS
┌──────────────▼──────────────────────────────────────────┐
│  Laravel 12 Application                                  │
│  ┌─────────────────────────────────────────────────┐   │
│  │  Middleware Stack                                │   │
│  │  - VerifyCsrfToken (web)                        │   │
│  │  - Authenticate (auth)                          │   │
│  │  - RoleMiddleware (role:Admin|Technician|...)   │   │
│  └──────────────────────┬──────────────────────────┘   │
│                          │                               │
│  ┌───────────┐  ┌────────▼──────────┐  ┌────────────┐  │
│  │  Routes   │→ │   Controllers     │→ │   Models   │  │
│  │  web.php  │  │  (HTTP Layer)     │  │ (Eloquent) │  │
│  │  auth.php │  │  - Business Logic │  │            │  │
│  └───────────┘  │  - Validation     │  └─────┬──────┘  │
│                  └───────────────────┘        │         │
│                                               │         │
│  ┌────────────────────────────────────────────▼──────┐  │
│  │  Database (MySQL/PostgreSQL)                       │  │
│  │  13 tables, proper FK constraints & indexes        │  │
│  └────────────────────────────────────────────────────┘  │
└─────────────────────────────────────────────────────────┘
```

### 2.2 Role Access Matrix

| Feature | Admin | Receptionist | Technician |
|---|:---:|:---:|:---:|
| User Management | ✅ | ❌ | ❌ |
| Test / Category / Group Setup | ✅ | ❌ | ❌ |
| Patient Management | ✅ | ✅ | ❌ |
| Lab Order CRUD | ✅ | ✅ | ❌ |
| Sample Management | ✅ | ✅ | ❌ |
| Result Entry | ✅ | ❌ | ✅ |
| Result Verification | ✅ | ❌ | ✅ |
| Order Approval | ✅ | ❌ | ✅ |
| Report Download | ✅ | ✅ | ❌ |
| Notification Log | ✅ | ✅ | ❌ |
| Public Report (QR) | N/A (token) | N/A | N/A |

### 2.3 Order Lifecycle

```
  [Receptionist Creates Order]
           │
           ▼
       PENDING ──────────────────────────────► [Deleted]
           │  (sample generated)
           ▼
      IN_PROGRESS ◄──── (result removed)
           │  (all tests verified)
           ▼
       COMPLETED
           │  (admin/technician approval)
           ▼
       APPROVED ──► QR Token Created ──► Email Notification
```

---

## 3. Database Schema & Migrations

### 3.1 Schema Quality

**18 migrations covering 13 tables.** Overall schema design is solid.

#### Strengths

- ✅ Foreign keys with `cascadeOnDelete` where appropriate (pivot tables, child records)
- ✅ Composite unique constraints (`lab_order_id, test_group_id` and `lab_order_id, test_id`) prevent duplicate test entries
- ✅ Decimal columns for monetary values (`total_amount`, `group_price_snapshot`, `price`) — no float rounding issues
- ✅ Enum columns for controlled vocabularies (status, gender, data_type, channel)
- ✅ Snapshots of reference ranges captured at order creation time (test results remain historically accurate even if ranges change)
- ✅ Strategic indexes on frequently queried columns: `(patient_id, status)` on lab_orders, `(lab_order_id, status)` on lab_order_tests, `(is_abnormal)` on lab_order_tests

#### Issues Found

| Severity | Table | Issue |
|---|---|---|
| ✅ Intentional | `patients` | `email` has no UNIQUE constraint — by design, patients (especially elderly) may share a relative's email address |
| 🟡 Medium | `qr_tokens` | `expires_at` is nullable with no default — tokens are permanent unless manually expired |
| 🟡 Medium | All tables | No `deleted_at` (soft deletes) — hard deletes break audit trails and referential history |
| 🟡 Medium | `notifications` | Missing composite index on `(patient_id, status)` and `(lab_order_id, status)` |
| 🟡 Medium | `lab_samples` | Sample code generation uses a counter without atomic locking — race condition under concurrent inserts |
| 🟢 Low | `patients` | No format constraint on `phone` — accepts any string |
| 🟢 Low | `lab_order_tests` | `result_value` is `text` with no max length constraint |

#### Recommended Migration Fixes

```php
// Fix 1: QR token default expiry (set in application, not migration)
// In LabOrderController::ensureQrToken()
'expires_at' => now()->addDays(30)

// Fix 2: Add missing notification indexes
$table->index(['patient_id', 'status']);
$table->index(['lab_order_id', 'status']);

// Fix 3: Soft deletes on critical tables
$table->softDeletes(); // patients, lab_orders, tests
```

---

## 4. Security Vulnerabilities

### 4.1 Critical

#### SEC-01 — No Rate Limiting on Public Report Endpoint
**File:** `routes/web.php`
**Risk:** An attacker can enumerate QR tokens by brute-force requesting `/reports/access/{token}`. Even with 64 random chars, unrestricted requests allow automated scanning.

```php
// Current (vulnerable)
Route::get('/reports/access/{token}', [PublicReportController::class, 'show'])
    ->name('public-reports.show');

// Fix
Route::get('/reports/access/{token}', [PublicReportController::class, 'show'])
    ->middleware('throttle:10,1')  // 10 req/min per IP
    ->name('public-reports.show');
```

#### SEC-02 — No Authorization Policies (Resource Ownership)
**Files:** All controllers
**Risk:** A Receptionist authenticated to the system can access any patient or lab order, not just their own. There are no `Gate` or `Policy` checks verifying resource ownership.

```php
// Missing everywhere — example for LabOrder
public function show(LabOrder $labOrder)
{
    $this->authorize('view', $labOrder);  // ← MISSING
    // ...
}

// Needed: app/Policies/LabOrderPolicy.php
public function view(User $user, LabOrder $order): bool
{
    return $user->role->name === 'Admin'
        || $user->id === $order->created_by;
}
```

### 4.2 High

#### SEC-03 — Synchronous Email Sending Can Leak Stack Traces
**File:** `app/Http/Controllers/LabOrderController.php`
**Risk:** `Mail::to()->send()` runs synchronously. On SMTP failure, an unhandled exception returns a 500 with stack trace visible to the client if `APP_DEBUG=true` in any non-local environment.

```php
// Current
Mail::to($patient->email)->send(new ReportReadyMail(...));

// Fix — queue it
Mail::to($patient->email)->queue(new ReportReadyMail(...));
// Also set QUEUE_CONNECTION in .env
```

#### SEC-04 — Missing `use` Imports in NotificationController
**File:** `app/Http/Controllers/NotificationController.php` (lines ~109–110)
**Risk:** `Mail` facade and `ReportReadyMail` are referenced without `use` imports. This causes a fatal error when the retry endpoint is called.

```php
// Add at top of NotificationController.php
use Illuminate\Support\Facades\Mail;
use App\Mail\ReportReadyMail;
```

#### ~~SEC-05 — QR Tokens Never Expire~~ ✅ FIXED
**File:** `app/Http/Controllers/LabOrderController.php` → `ensureQrToken()`
**Fix Applied:** `expires_at` now set to `now()->addDays(30)` on token creation. `PublicReportController` already validates expiry correctly.

```php
// Fix: set expiry on token creation
QrToken::create([
    'lab_order_id' => $order->id,
    'token'        => bin2hex(random_bytes(32)),
    'is_active'    => true,
    'expires_at'   => now()->addDays(30),  // ← ADD THIS
]);
```

### 4.3 Medium

#### SEC-06 — Plaintext Sensitive Patient Data
**File:** `app/Models/Patient.php`
Patient PII (phone, email, address) and lab results are stored in plaintext. If the database is compromised, all patient data is immediately readable.

```php
// Optional improvement using Laravel encrypted casting
protected $casts = [
    'phone'   => 'encrypted',
    'email'   => 'encrypted',
    'address' => 'encrypted',
];
```
> Note: Encrypted fields cannot be used in WHERE clauses; factor this into the design.

#### SEC-07 — Email Not Verified Before Access
**File:** `app/Models/User.php`
The `MustVerifyEmail` interface is not implemented (or email verification is not enforced via middleware). Users can log in and perform actions without verifying their email.

#### SEC-08 — Race Condition in Sample Code Generation
**File:** `app/Http/Controllers/LabSampleController.php`
The sample sequence number is computed by counting existing samples and incrementing. Under concurrent requests, two samples for the same order could receive the same code.

```php
// Replace counter approach with DB-level atomic increment
// Use a database sequence or a SELECT FOR UPDATE lock:
$seq = DB::table('lab_samples')
    ->where('lab_order_id', $labOrder->id)
    ->lockForUpdate()
    ->count() + 1;
```

### 4.4 Low

| ID | Description | File |
|---|---|---|
| SEC-09 | No phone number format validation — accepts any string | `PatientController`, `UpdateProfileRequest` |
| SEC-10 | `notes` field in orders has no max-length validation | `LabOrderController` |
| SEC-11 | HTML rendered in DataTables `rawColumns()` — ensure Blade partials use `{{ }}` not `{!! !!}` | Multiple controllers |
| SEC-12 | No Content Security Policy (CSP) headers configured | `app/Http/Middleware` |
| SEC-13 | Sessions table has no cleanup scheduled | `routes/console.php` |

---

## 5. Code Quality

### 5.1 Fat Controllers

The most significant structural issue. `LabOrderController` is 449+ lines embedding business logic that belongs in service classes.

**Affected controllers and their line counts (estimated):**

| Controller | Lines | Problem |
|---|---|---|
| `LabOrderController` | ~449 | Order creation, QR token, notification orchestration, reference range resolution |
| `TestController` | ~276 | Complex reference range validation inline |
| `LabResultController` | ~219 | Result lifecycle, abnormal detection, status refresh |
| `LabSampleController` | ~148 | Sample sequencing, barcode data prep |

**Recommended service extraction:**

```
app/Services/
├── OrderCreationService.php       // store() logic from LabOrderController
├── ReferenceRangeResolver.php     // resolveReferenceRange()
├── OrderStatusService.php         // refreshOrderStatus() from LabResultController
├── NotificationService.php        // queuePatientNotifications()
└── SampleCodeGenerator.php        // sample code generation
```

### 5.2 Magic Strings

Status values and enums are repeated as raw strings throughout controllers, models, and views with no centralized definition.

**Examples found:**

```php
// Scattered across multiple files:
'pending', 'in_progress', 'completed', 'approved'   // order statuses
'entered', 'verified'                                 // test statuses
'collected', 'received', 'in_process', 'rejected'    // sample statuses
'email', 'sms'                                        // notification channels
'numeric', 'text'                                     // test data types
```

**Fix:** Create PHP 8.1 backed enums:

```php
// app/Enums/OrderStatus.php
enum OrderStatus: string {
    case Pending    = 'pending';
    case InProgress = 'in_progress';
    case Completed  = 'completed';
    case Approved   = 'approved';
}
```

### 5.3 Missing Test Suite

**No automated tests found** in `tests/Feature/` or `tests/Unit/`. This is the highest-risk gap for a medical system where incorrect result calculations or status transitions could have patient safety implications.

**Minimum recommended test coverage:**

| Test | Type | Priority |
|---|---|---|
| Reference range resolution (gender+age logic) | Unit | 🔴 Critical |
| Abnormal flag calculation | Unit | 🔴 Critical |
| Order status transitions | Feature | 🔴 Critical |
| Order creation with snapshot data integrity | Feature | 🔴 Critical |
| Role-based route access | Feature | 🔴 Critical |
| QR token validation | Feature | 🟡 High |
| PDF report generation | Feature | 🟡 High |
| Sample code uniqueness | Unit | 🟡 High |

### 5.4 Positive Code Quality Observations

- ✅ `DB::transaction()` used correctly in complex multi-insert operations (`LabOrderController::store`, `LabResultController::bulkUpdateResults`)
- ✅ Eager loading used consistently — `$order->load(['patient', 'groups.testGroup', 'tests.test'])` pattern prevents N+1
- ✅ `resolveReferenceRange()` handles edge cases well: prefers gender-specific ranges, narrows age bands, falls back gracefully
- ✅ Historical snapshots in `lab_order_tests` (test_name, unit, ref_min, ref_max) ensure result integrity even when test definitions change later
- ✅ `patientAge()` gracefully handles invalid DOB via try-catch with null fallback
- ✅ Self-deletion prevention in `UserController::destroy()`
- ✅ Prevents result editing on approved orders

### 5.5 Missing PHPDoc / Type Hints

Controller methods lack return type declarations and PHPDoc blocks. While not functionally harmful, this hinders IDE support and maintainability.

```php
// Current
public function store(Request $request)

// Better
public function store(StoreLabOrderRequest $request): RedirectResponse
```

---

## 6. Database Query Efficiency

### 6.1 Confirmed Good Practices

- ✅ DataTables uses server-side processing (no full table loads)
- ✅ `->with()` / `->load()` eager loading observed
- ✅ Indexes on high-traffic query columns

### 6.2 Identified N+1 Risks

#### N+1-01 — LabOrderController::index() Patient Names
**File:** `app/Http/Controllers/LabOrderController.php`

DataTables queries often access `$order->patient->full_name` in closures or view partials. If `patient` is not eager-loaded in the base query, this generates one query per row.

```php
// Ensure the base query includes:
LabOrder::with(['patient', 'creator'])->...
```

#### N+1-02 — NotificationController::index()
If the notifications DataTables query does not eager-load `patient` and `labOrder`, each rendered row fires two additional queries.

```php
Notification::with(['patient', 'labOrder'])->...
```

#### N+1-03 — LabResultController::index()
Loading an order with all tests and their groups:

```php
// Ensure full eager load chain:
$labOrder->load([
    'patient',
    'groups.testGroup',
    'tests.test.ranges',  // needed for reference display
]);
```

### 6.3 Reference Range Resolution Performance

`resolveReferenceRange()` in `LabOrderController::store()` is called **once per test per order**. For an order with 20 tests, this fires up to 20 separate queries against `test_reference_ranges`.

**Fix — Batch load all ranges for the order's tests upfront:**

```php
// Before the loop, preload all ranges for all tests in the order
$testIds = $selectedTests->pluck('id');
$allRanges = TestReferenceRange::whereIn('test_id', $testIds)->get()->groupBy('test_id');

// Pass $allRanges into the resolver instead of querying per test
```

### 6.4 Missing Pagination

`NotificationController::index()` — if paginated via DataTables, this is acceptable. However verify the DataTables query uses `->paginate()` or `->skip()->take()` and not `->get()` on the full table.

### 6.5 Potential Missing Index

The `test_reference_ranges` table is queried by `(test_id, gender)` for range resolution. Verify the index `(test_id, gender)` declared in the migration is present:

```sql
-- Confirm index exists:
SHOW INDEX FROM test_reference_ranges WHERE Key_name LIKE '%test_id%';
```

---

## 7. Feature Completeness

*No formal proposal document was found in the repository. Assessment is based on inferred MLMRS requirements for a medical laboratory management system.*

### 7.1 Core Module Status

| Module | Status | Notes |
|---|---|---|
| **User & Role Management** | ✅ Complete | Admin CRUD for users; 3 roles |
| **Patient Registration** | ✅ Complete | Auto patient code, DOB, gender, contact |
| **Test Catalog** | ✅ Complete | Tests, categories, groups with pricing |
| **Reference Ranges** | ✅ Complete | Gender + age-based ranges with overlap detection |
| **Lab Order Creation** | ✅ Complete | Group-based selection, auto range snapshot |
| **Sample Tracking** | ✅ Complete | Batch sample creation, printable barcode labels |
| **Result Entry** | ✅ Complete | Bulk entry, abnormal detection, data type enforcement |
| **Result Verification** | ✅ Complete | Technician verification workflow with audit trail |
| **Order Approval** | ✅ Complete | Final approval gate before report release |
| **PDF Report Generation** | ✅ Complete | DomPDF with approved-order gate |
| **QR Code Public Access** | ✅ Complete | Token-based public report URL via QR |
| **Email Notifications** | ✅ Complete | Report-ready email on approval |
| **Notification Log** | ✅ Complete | DataTables log with retry |
| **Dashboard** | ⚠️ Partial | Route exists (`/` → dashboard view) but not analyzed |
| **SMS Notifications** | ❌ Missing | Notification model has `channel=sms` but no SMS provider integrated; retry stub only |
| **Report Revision Workflow** | ❌ Missing | No mechanism to un-approve and re-enter results |
| **Audit Trail / Activity Log** | ❌ Missing | No `activity_logs` table or logging package |
| **Automated Tests** | ❌ Missing | No PHPUnit/Pest tests |
| **Print / Export Tests** | ⚠️ Partial | PDF export present; no CSV/Excel export |

### 7.2 Missing Features Detail

#### Missing: SMS Notification Provider
The `Notification` model supports `channel = 'sms'` and notifications are created for SMS in `queuePatientNotifications()`. However no SMS provider (Twilio, Africa's Talking, etc.) is integrated. The retry method in `NotificationController` explicitly skips SMS with a TODO comment.

**Implementation path:**
```php
// composer require twilio/sdk or vonage/client
// In NotificationService::sendSms()
$twilio->messages->create($patient->phone, [
    'from' => config('services.twilio.from'),
    'body' => $notification->message,
]);
```

#### Missing: Report Revision / Amendment
Once approved, there is no path to re-enter or amend results. For a real lab this is necessary for corrected reports.

**Suggested approach:**
- Add `parent_order_id` FK to `lab_orders` for amended reports
- Add `is_amended` boolean and `amendment_reason` field
- Allow Admin to "recall" an approved order, resetting it to `in_progress`

#### Missing: Audit Trail
No logging of who viewed, printed, or downloaded reports. For medical compliance (HIPAA-adjacent requirements) an audit trail is typically required.

**Quick implementation:**
```php
// composer require spatie/laravel-activitylog
// In models:
use Spatie\Activitylog\Traits\LogsActivity;
```

---

## 8. Validation & Error Handling

### 8.1 Strengths

- ✅ Form Request classes used for profile updates
- ✅ Inline validation in controllers uses Laravel's `validate()` with descriptive rules
- ✅ Reference range validation catches overlaps, duplicates, and invalid min/max pairs
- ✅ `try-catch` around `Mail::send()` to prevent uncaught exceptions
- ✅ DOB parsing has fallback to null on `Exception`
- ✅ Flash messages (`session()->flash()`) used consistently for user feedback

### 8.2 Issues

| Severity | Location | Issue |
|---|---|---|
| 🔴 High | `NotificationController` | Missing `use Mail` / `use ReportReadyMail` imports — retry endpoint throws fatal error |
| 🟡 Medium | All controllers | No `FormRequest` classes for most endpoints — validation rules inline in controllers |
| 🟡 Medium | `LabOrderController` | No explicit handling of `LabOrder::findOrFail()` 404 — relies on Laravel's default exception handler |
| 🟡 Medium | `LabResultController` | Numeric validation uses basic `is_numeric()` PHP check — does not enforce max decimal precision |
| 🟢 Low | `PatientController` | Phone field accepts any string; no regex validation |
| 🟢 Low | Multiple | No `max:` rule on long text fields (notes, address, rejection_reason) |

### 8.3 Recommended FormRequest Extraction

```
app/Http/Requests/
├── StoreLabOrderRequest.php       // Currently inline in LabOrderController::store
├── StoreLabResultRequest.php      // Currently inline in LabResultController::bulkUpdateResults
├── StorePatientRequest.php        // Currently inline in PatientController::store
├── StoreTestRequest.php           // Currently inline in TestController::store
└── UpdateLabOrderRequest.php
```

---

## 9. Dependency Assessment

### 9.1 Production Dependencies

| Package | Version | Assessment |
|---|---|---|
| `laravel/framework` | ^12.0 | ✅ Latest major — good |
| `barryvdh/laravel-dompdf` | ^3.1 | ✅ Standard PDF choice for Laravel |
| `yajra/laravel-datatables-oracle` | ^12.7 | ✅ Appropriate for server-side tables |
| `simplesoftwareio/simple-qrcode` | ^4.2 | ✅ Stable QR library |
| `milon/barcode` | ^13.1 | ✅ Works for sample labels |
| `blade-ui-kit/blade-heroicons` | ^2.6 | ✅ Standard icon set |
| `laravel/tinker` | ^2.10.1 | ⚠️ Ensure disabled in production (`APP_ENV=production`) |

### 9.2 Missing / Recommended Packages

| Package | Purpose | Priority |
|---|---|---|
| `spatie/laravel-activitylog` | Audit trail | 🔴 High |
| `spatie/laravel-permission` | Robust RBAC replacing custom middleware | 🟡 Medium |
| `laravel/horizon` | Queue monitoring for notifications | 🟡 Medium |
| `barryvdh/laravel-debugbar` | Dev-only query debugging | 🟢 Low (dev only) |

---

## 10. Recommendations & Action Plan

### Priority 1 — Fix Before Any Production Deployment

| # | Action | File(s) | Effort |
|---|---|---|---|
| P1-1 | Add `throttle:10,1` middleware to public report route | `routes/web.php` | 5 min |
| P1-2 | Fix missing `use Mail` and `use ReportReadyMail` in NotificationController | `NotificationController.php` | 5 min |
| P1-3 | Set `expires_at` default (e.g., 30 days) when creating QrTokens | `LabOrderController.php` | 10 min |
| P1-5 | Replace synchronous `Mail::send()` with `Mail::queue()` | `LabOrderController.php`, `NotificationController.php` | 20 min |
| P1-6 | Add `lockForUpdate()` to sample sequence counter | `LabSampleController.php` | 15 min |

### Priority 2 — Security & Authorization Hardening

| # | Action | Effort |
|---|---|---|
| P2-1 | Create `LabOrderPolicy`, `PatientPolicy` — check resource ownership | 2 hrs |
| P2-2 | Implement `MustVerifyEmail` on User model | 30 min |
| P2-3 | Add phone format validation (regex) to Patient forms | 30 min |
| P2-4 | Review all DataTables `rawColumns()` — audit Blade partials for `{!! !!}` vs `{{ }}` | 1 hr |
| P2-5 | Add CSP and security headers via middleware | 1 hr |

### Priority 3 — Code Quality & Maintainability

| # | Action | Effort |
|---|---|---|
| P3-1 | Create PHP 8.1 enums for `OrderStatus`, `TestStatus`, `SampleStatus`, `NotificationChannel` | 1 hr |
| P3-2 | Extract `ReferenceRangeResolver` service class | 2 hrs |
| P3-3 | Extract `OrderCreationService` from LabOrderController | 3 hrs |
| P3-4 | Extract `NotificationService` | 1 hr |
| P3-5 | Convert inline controller validation to FormRequest classes | 2 hrs |
| P3-6 | Add `deleted_at` (soft deletes) to `patients`, `lab_orders`, `tests` | 1 hr |

### Priority 4 — Test Coverage

| # | Test | Type |
|---|---|---|
| T1 | Reference range resolution — gender/age permutations | Unit |
| T2 | Abnormal flag calculation edge cases (null ref range, boundary values) | Unit |
| T3 | Order status auto-transitions on result entry/verification | Feature |
| T4 | Role middleware blocks unauthorized access | Feature |
| T5 | Public report: valid token, expired token, inactive token, wrong token | Feature |
| T6 | Order creation snapshot data integrity | Feature |
| T7 | Sample code uniqueness under concurrent creation | Feature |

### Priority 5 — Feature Completions

| # | Feature | Notes |
|---|---|---|
| F1 | Integrate SMS provider (Twilio / Africa's Talking) | Replace TODO stub |
| F2 | Report amendment / corrected report workflow | New `parent_order_id` + `is_amended` fields |
| F3 | Audit trail via `spatie/laravel-activitylog` | Log access to reports |
| F4 | Batch preload reference ranges in `store()` | Performance — eliminate per-test queries |
| F5 | Add pagination / DataTables to NotificationController index | Correctness under scale |

---

## Appendix A — Critical Code Locations

| Item | File | Line(s) |
|---|---|---|
| Missing rate limit | `routes/web.php` | Public report route |
| Missing Mail imports | `app/Http/Controllers/NotificationController.php` | ~109–110 |
| Sync email send | `app/Http/Controllers/LabOrderController.php` | `approve()` method |
| QR token no expiry | `app/Http/Controllers/LabOrderController.php` | `ensureQrToken()` |
| Race condition | `app/Http/Controllers/LabSampleController.php` | `store()` sequence count |
| No ownership check | All resource controllers | `show()`, `edit()`, `update()`, `destroy()` |
| Reference range N+1 | `app/Http/Controllers/LabOrderController.php` | `resolveReferenceRange()` loop |

## Appendix B — Files With No Issues

The following files are well-implemented with no significant issues:

- `app/Http/Middleware/RoleMiddleware.php` — clean, handles null roles safely
- `app/Models/Patient.php` — clean auto-code generation in `boot()`
- `app/Http/Controllers/LabReportController.php` — simple, correctly gates on `approved` status
- `app/Http/Controllers/PublicReportController.php` — correct token validation logic (just needs rate limiting)
- `app/Http/Controllers/TestCategoryController.php` — straightforward CRUD
- All migration files — properly structured with appropriate constraints

---

*Generated by Claude Code automated static analysis — 2026-03-15*
