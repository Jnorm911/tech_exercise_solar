# Data Integrity Report

Generated: 2026-03-23
Database: `database/database.sqlite`
Total projects scanned: 160

---

## Issue 1 — `approved_at` set but `status != 'approved'`

**29 records affected**

| ID | Status | approved_at |
|----|--------|-------------|
| 118 | paid | 2024-01-08 |
| 121 | paid | 2024-05-02 |
| 131 | paid | 2024-12-20 |
| 132 | paid | 2024-09-10 |
| 138 | paid | 2024-07-21 |
| 153 | paid | 2024-01-07 |
| 154 | paid | 2024-03-27 |
| 163 | paid | 2024-01-23 |
| 174 | paid | 2024-10-16 |
| 176 | paid | 2024-08-03 |
| 177 | paid | 2024-03-18 |
| 191 | paid | 2024-01-24 |
| 192 | paid | 2024-09-18 |
| 193 | paid | 2024-10-15 |
| 205 | paid | 2024-03-03 |
| 207 | paid | 2024-05-31 |
| 208 | paid | 2024-03-08 |
| 211 | paid | 2024-11-20 |
| 212 | paid | 2024-12-10 |
| 215 | paid | 2024-08-28 |
| 216 | paid | 2024-02-13 |
| 225 | paid | 2024-09-17 |
| 226 | paid | 2025-01-24 |
| 227 | paid | 2024-07-30 |
| 229 | paid | 2024-05-26 |
| 234 | paid | 2024-04-23 |
| 238 | paid | 2024-03-31 |
| 239 | paid | 2024-12-26 |
| 241 | paid | 2024-08-30 |

**Root cause:** All affected records have `status = 'paid'`. This suggests a `paid` status was added to the workflow after `approved_at` was already being set, and the transition logic was never updated to clear or re-map the timestamp.

**Recommended fix:** Decide whether `paid` is a downstream state of `approved` (if so, `approved_at` is valid and intentional — document this in the status enum). If `paid` is a separate flow, clear `approved_at` on these records and add a `paid_at` column instead.

---

## Issue 2 — `approved_at` before `submitted_at` (impossible timestamp)

**1 record affected**

| ID | submitted_at | approved_at |
|----|-------------|-------------|
| 251 | 2024-06-15 14:30:00 | 2024-06-14 10:00:00 |

**Root cause:** `approved_at` is 28 hours before `submitted_at`. Likely a data entry error or a seeder bug.

**Recommended fix:** Null out `approved_at` on record 251 and set `status` back to `submitted` pending manual review.

---

## Issue 3 — `submitted_at` null on non-draft project

**1 record affected**

| ID | Status |
|----|--------|
| 253 | submitted |

**Root cause:** Project reached `submitted` status without a `submitted_at` timestamp being recorded. Likely a missing timestamp assignment in the submission handler.

**Recommended fix:** Backfill `submitted_at` from `updated_at` on record 253 as an approximation, then add a DB-level `NOT NULL` constraint on `submitted_at` for all non-draft statuses.

---

## Issue 4 — Projects with no `ahj_id` (orphaned records)

**0 records affected.** No orphaned projects found.

---

## Issue 5 — Duplicate project titles within the same AHJ

**0 records affected.** No duplicates found.

---

## Issue 6 — `status = 'approved'` but `approved_at` null

**1 record affected**

| ID | Status |
|----|--------|
| 252 | approved |

**Root cause:** Project was marked `approved` but no timestamp was written. Likely a bug in the approval handler where status is set but `approved_at` is not.

**Recommended fix:** Backfill `approved_at` from `updated_at` on record 252 as an approximation, then add application-level validation to always set `approved_at` when transitioning to `approved` status.

---

## Summary

| Check | Issues Found |
|-------|-------------|
| `approved_at` set but status not `approved` | 29 records |
| `approved_at` before `submitted_at` | 1 record |
| `submitted_at` null on non-draft | 1 record |
| Orphaned records (no `ahj_id`) | 0 |
| Duplicate titles within AHJ | 0 |
| `approved` status with null `approved_at` | 1 record |

**Most critical:** Issue 1 (29 records) — requires a product decision on whether `paid` is a valid post-approval state before any data fix is applied.
