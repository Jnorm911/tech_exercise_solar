<?php

namespace App\Http\Controllers\Ahj;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Ahj;
// [A1] Removed unused imports: Project and Carbon were never referenced

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        // MOCK AUTH: replace with auth()->user()->ahj in production
        $ahj = auth()->user()?->ahj ?? Ahj::first();

        if (!$ahj) {
            abort(500, 'Database is empty. Did you run the migration/seeder?');
        }

        // [A2] One base query scoped to this AHJ — cloned below so it's never called twice
        $query = $ahj->projects();

        // [A3] Date filter: only applied when present — blank input shows all data, never crashes
        $startDate = $request->query('start_date');
        $endDate = $request->query('end_date');
        if ($startDate) {
            $query->whereDate('submitted_at', '>=', $startDate);
        }
        if ($endDate) {
            $query->whereDate('submitted_at', '<=', $endDate);
        }

        // [A4] Avg approval time: SQLite julianday() diff in SQL — no rows loaded into PHP memory
        $avgHours = (clone $query)
            ->where('status', 'approved')
            ->whereNotNull('submitted_at')
            ->whereNotNull('approved_at')
            ->selectRaw('AVG((julianday(approved_at) - julianday(submitted_at)) * 24) as avg_hours')
            ->value('avg_hours');

        // [A5] SQL aggregates for counts — DB does the work, not PHP loops on a loaded collection
        $stats = [
            'total_projects'    => $query->count(),
            'approved_projects' => (clone $query)->where('status', 'approved')->count(),
            'pending_projects'  => (clone $query)->whereIn('status', ['submitted', 'revision_required'])->count(),
            'avg_approval_time' => $avgHours
                ? floor($avgHours / 24) . ' days, ' . round(fmod($avgHours, 24)) . ' hours'
                : 'N/A',
        ];

        // [A6] paginate(20) returns a Paginator object — carries page metadata and ->links() for the view
        // withQueryString() keeps date filter params in pagination URLs so the view stays shareable
        $projects = (clone $query)->latest('submitted_at')->paginate(20)->withQueryString();

        return view('pages.ahj.dashboard', [
            'ahj'      => $ahj,
            'stats'    => $stats,
            'projects' => $projects,
        ]);
    }
}
