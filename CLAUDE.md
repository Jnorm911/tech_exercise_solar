# CLAUDE.md — Agent Instructions

This file defines the workflow, guardrails, and standards for every Claude Code session on this project. Read it before doing anything.

---

## Professional Standards (Non-negotiable)

- **KISS** — Keep It Simple, Stupid. If there are two ways to do it, use the simpler one.
- **DRY** — Don't Repeat Yourself. One source of truth. No copy-pasted logic.
- These are upfront requirements, not afterthoughts.

---

## How Each Session Starts

1. I will point you to the file we are working on. Do not assume — wait until I do.
2. I will read the file and tell you what I think it's doing.
3. You correct me if I'm wrong.
4. You ask me what I think the fix or feature should look like.
5. I propose an approach.
6. You correct my approach if needed.
7. You write the code.
8. You quiz me on what you wrote — I must explain it back to you.
9. You correct any gaps in my understanding.
10. We update the study notes.

This is a teaching workflow. The goal is not just working code — it's working code I can explain line-by-line under pressure.

---

## Session Management

- **One chat per ticket.** This is a professional discipline: a fresh context per task prevents drift and keeps the scope clear.
- Monitor chat length. If the session is getting long, wrap up cleanly and note where we left off before starting a new one.

---

## Agent Boundaries

| Agent | Owns | Cannot Touch |
|---|---|---|
| A (Backend) | `DashboardController.php` | `dashboard.blade.php`, DB data |
| B (Frontend) | `dashboard.blade.php` | `DashboardController.php`, DB data |
| C (Data Integrity) | `DATA_INTEGRITY_REPORT.md` | All application code |

Do not cross these boundaries. Do not "helpfully" fix things in a file you don't own.

---

## Hard Rules for Agent A

- All queries start from `$ahj->projects()`. Never `Project::all()` or `Project::query()`. Multi-tenancy is enforced here, not in review.
- No full table loads. Use DB aggregates (`->count()`, `->avg()`). Never `->get()` for counting.
- Date filter must default to no filter on blank input. Never crash on missing input.
- `clone $query` before adding extra constraints. Never stack conditions on the base query.

## Hard Rules for Agent B

- No `<script>` blocks. Pure Blade/HTML only.
- Use `@forelse` / `@empty` — never a bare `@foreach` that silently renders a blank table.
- Date filter form must use GET, not POST. Filtered views must be URL-shareable.

## Hard Rules for Agent C

- Read-only. No changes to application code under any circumstances.
- Write findings to `DATA_INTEGRITY_REPORT.md` only.
- Flag ambiguous data issues as requiring a product decision — do not silently "fix" data.

---

## Why This Approach

For a project of this scope, a single well-structured session with clear file boundaries and guardrails per task was the right call. In production I run one session per ticket to prevent context drift — that discipline applied here too, it just fit cleanly within one session. The boundaries and rules above would scale to parallel agents without modification.
