# SolarAPP+ Dashboard

A Laravel dashboard for AHJ (Authority Having Jurisdiction) permit reviewers to track solar project submissions, approvals, and processing times.

## What this project demonstrates

A multi-agent AI engineering workflow that delivered three independent changes in parallel:

- **Backend** — date range filtering, SQL-level avg approval time, paginated results
- **Frontend** — filter form, stat cards, pagination nav, null-safe rendering
- **Data integrity** — automated DB scan with findings and recommended fixes

See `PROCESS.md` for how the workflow was designed and `DATA_INTEGRITY_REPORT.md` for findings.

---

## Key technical decisions

| Decision | Why |
|---|---|
| `->paginate(20)` not `->limit()` | Returns a Paginator object with page metadata and `->links()` nav for free |
| Avg approval time in SQL | `julianday()` diff runs on the DB — no rows loaded into PHP memory |
| `clone $query` for each stat | Eloquent builders are mutable — clone keeps the base query clean for reuse |
| GET params for date filter | Keeps the filtered URL shareable and bookmarkable |
| All queries start from `$ahj->projects()` | Enforces AHJ-level multi-tenancy at the controller — never crosses tenant data |

---

## Running locally

```bash
composer install
php artisan serve
```

Visit `http://127.0.0.1:8000`

---

## Stack

- Laravel 11, PHP 8.2
- SQLite (160 seeded projects, 2 AHJs)
- Blade + Tailwind CSS
