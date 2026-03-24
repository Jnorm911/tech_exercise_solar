@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="mb-8">
        <h1 class="text-2xl font-bold mb-2">Welcome, {{ $ahj->name }}</h1>
        <p class="text-gray-600">Here is your jurisdiction's activity.</p>
    </div>

    {{-- [B3] GET form: submits as URL params so the filtered view is shareable/bookmarkable --}}
    <form method="GET" action="" class="flex gap-4 mb-8">
        <input type="date" name="start_date" value="{{ request('start_date') }}" class="border rounded px-3 py-2">
        <input type="date" name="end_date" value="{{ request('end_date') }}" class="border rounded px-3 py-2">
        <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded">Filter</button>
        <a href="{{ request()->url() }}" class="px-4 py-2 text-gray-600">Clear</a>
    </form>

    <!-- Statistics Cards -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
        <div class="bg-white p-6 rounded-lg shadow">
            <h3 class="text-gray-500 text-sm uppercase">Total Projects</h3>
            <p class="text-3xl font-bold">{{ $stats['total_projects'] }}</p>
        </div>
        <div class="bg-white p-6 rounded-lg shadow">
            <h3 class="text-gray-500 text-sm uppercase">Approved</h3>
            <p class="text-3xl font-bold text-green-600">{{ $stats['approved_projects'] }}</p>
        </div>
        {{-- [B4] Pending card: was passed from controller but never displayed --}}
        <div class="bg-white p-6 rounded-lg shadow">
            <h3 class="text-gray-500 text-sm uppercase">Pending</h3>
            <p class="text-3xl font-bold text-yellow-600">{{ $stats['pending_projects'] }}</p>
        </div>
        <div class="bg-white p-6 rounded-lg shadow">
            <h3 class="text-gray-500 text-sm uppercase">Avg. Approval Time</h3>
            <p class="text-3xl font-bold text-blue-600">{{ $stats['avg_approval_time'] }}</p>
        </div>
    </div>

    <!-- Projects List -->
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <table class="min-w-full">
            <thead class="bg-gray-50 text-left">
                <tr>
                    <th class="px-6 py-3 text-xs font-medium text-gray-500 uppercase">Project Title</th>
                    <th class="px-6 py-3 text-xs font-medium text-gray-500 uppercase">Status</th>
                    <th class="px-6 py-3 text-xs font-medium text-gray-500 uppercase">Submitted Date</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                @forelse($projects as $project)
                <tr>
                    <td class="px-6 py-4">{{ $project->title }}</td>
                    <td class="px-6 py-4">{{ $project->status }}</td>
                    {{-- [B1] ?-> null-safe operator: submitted_at is nullable on draft projects --}}
                    <td class="px-6 py-4">{{ $project->submitted_at?->format('M d, Y') ?? '—' }}</td>
                </tr>
                {{-- [B2] @empty: renders a message instead of a blank table when no projects match --}}
                @empty
                <tr>
                    <td class="px-6 py-4 text-gray-500" colspan="3">No projects found.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- [B5] Blade's built-in paginator — no custom JS, links carry date params via withQueryString() --}}
    <div class="mt-4">
        {{ $projects->links() }}
    </div>
</div>
@endsection
